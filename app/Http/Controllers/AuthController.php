<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Blog;
use App\Models\UserLogin;
use App\Models\Setting;

class AuthController extends Controller
{
    private function verifyTurnstile(Request $request)
    {
        $token = $request->input('cf-turnstile-response');
        if (!$token) {
            return true; // Optional if not enforced or in development
        }

        $secret = Setting::get('turnstile_secret_key', '0x4AAAAAAE8QOkJScve1toziPVGAdBajjJI');
        try {
            $resp = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);
            return $resp->json('success') === true;
        } catch (\Throwable $e) {
            return true;
        }
    }

    private function ensureEmailVerificationColumns()
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'email_verified_at')) {
                \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->timestamp('email_verified_at')->nullable()->after('email');
                });
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'email_verification_token')) {
                \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('email_verification_token', 64)->nullable()->after('email_verified_at');
                });
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'notify_new_follower')) {
                \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->boolean('notify_new_follower')->default(true)->after('status');
                });
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'notify_private_message')) {
                \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->boolean('notify_private_message')->default(true)->after('notify_new_follower');
                });
            }
            if (!\Illuminate\Support\Facades\Schema::hasTable('messages')) {
                \Illuminate\Support\Facades\Schema::create('messages', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
                    $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
                    $table->string('subject', 190)->nullable();
                    $table->text('body');
                    $table->boolean('is_read')->default(false);
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {}
    }

    public function loginView(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $posts = \App\Models\Post::with(['blog.user'])->where('status', 'published')->orderBy('published_at', 'desc')->take(8)->get();
        $featuredBlogs = \App\Models\Blog::where('status', 'active')->where('is_featured', 1)->take(6)->get();
        $activeAds = \App\Models\Advertisement::where('is_active', true)->get();
        $open_modal = 'login';
        return view('explore', compact('posts', 'featuredBlogs', 'activeAds', 'open_modal'));
    }

    public function registerView(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $posts = \App\Models\Post::with(['blog.user'])->where('status', 'published')->orderBy('published_at', 'desc')->take(8)->get();
        $featuredBlogs = \App\Models\Blog::where('status', 'active')->where('is_featured', 1)->take(6)->get();
        $activeAds = \App\Models\Advertisement::where('is_active', true)->get();
        $open_modal = 'register';
        return view('explore', compact('posts', 'featuredBlogs', 'activeAds', 'open_modal'));
    }

    public function login(Request $request)
    {
        $this->ensureEmailVerificationColumns();

        $credentials = $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        if (!$this->verifyTurnstile($request)) {
            return back()->withErrors(['login' => 'Security challenge failed. Please try again.']);
        }

        $user = User::where('username', $credentials['login'])
                    ->orWhere('email', $credentials['login'])
                    ->first();

        if ($user && Hash::check($credentials['password'], $user->password_hash)) {
            if ($user->status === 'banned') {
                return back()->withErrors(['login' => 'This account has been permanently disabled.']);
            }
            if ($user->status === 'suspended') {
                return back()->withErrors(['login' => 'Your account is currently suspended. Please contact support.']);
            }

            // Check if email verification is enabled on signup
            $verificationEnabled = Setting::get('email_verification_enabled', '0') === '1';
            if ($verificationEnabled && !$user->isEmailVerified() && $user->role !== 'admin') {
                return redirect()->route('verification.notice')->with([
                    'unverified_email' => $user->email,
                    'unverified_username' => $user->username,
                    'error' => 'Please verify your email address (' . mask_email($user->email) . ') before signing in. Check your inbox or click below to request a new link.'
                ]);
            }

            Auth::login($user, (bool)$request->input('remember'));

            // Record login IP & user-agent
            try {
                UserLogin::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip() ?? '127.0.0.1',
                    'user_agent' => substr((string)$request->userAgent(), 0, 255),
                    'logged_at' => now(),
                ]);
            } catch (\Throwable $e) {}

            if ($user->role === 'admin') {
                return redirect()->intended('/admin');
            }
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['login' => 'Invalid credentials.']);
    }

    public function register(Request $request)
    {
        $this->ensureEmailVerificationColumns();

        $rawSubdomain = $request->input('subdomain') ?: $request->input('username');
        if ($rawSubdomain) {
            $cleanSubdomain = strtolower(trim(preg_replace('/(\.nina\.sucks|\.localhost.*)$/i', '', $rawSubdomain)));
            $cleanSubdomain = preg_replace('/[^a-z0-9_-]/', '-', $cleanSubdomain);
            $request->merge(['username' => $cleanSubdomain]);
        }

        $request->validate([
            'username' => 'required|alpha_dash|min:3|max:30|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if (!$this->verifyTurnstile($request)) {
            return back()->withErrors(['register' => 'Security challenge failed. Please try again.']);
        }

        // Handle referral
        $referredBy = null;
        $refCode = $request->input('ref');
        if ($refCode) {
            $referrer = User::where('referral_code', $refCode)->first();
            if ($referrer) {
                $referredBy = $referrer->id;
                $rewardPoints = (int)Setting::get('referral_reward_points', 100);
                $referrer->increment('points', $rewardPoints);
            }
        }

        $newRefCode = strtoupper(Str::random(8));

        // Check if email verification is enabled
        $verificationEnabled = Setting::get('email_verification_enabled', '0') === '1';
        $verificationToken = $verificationEnabled ? Str::random(48) : null;
        $verifiedAt = $verificationEnabled ? null : now();

        $user = User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'email_verified_at' => $verifiedAt,
            'email_verification_token' => $verificationToken,
            'password_hash' => Hash::make($request->input('password')),
            'role' => 'user',
            'points' => 100, // Starter welcome points
            'status' => 'active',
            'referral_code' => $newRefCode,
            'referred_by_user_id' => $referredBy,
        ]);

        Blog::create([
            'user_id' => $user->id,
            'slug' => $user->username,
            'title' => $user->username,
            'is_main' => 1,
            'color' => '#ff2bd6',
            'status' => 'active',
            'searchable' => 1,
            'show_profile' => 1,
        ]);

        // Email admin when a new user signs up
        try {
            send_admin_new_user_alert($user);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Admin new user notification error: ' . $e->getMessage());
        }

        // If email verification is enabled, send verification email and prompt user
        if ($verificationEnabled) {
            $verifyUrl = route('verification.verify', ['id' => $user->id, 'token' => $verificationToken]);
            try {
                send_verification_email($user->email, $user->username, $verifyUrl);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Verification email trigger error: ' . $e->getMessage());
            }

            return redirect()->route('verification.notice')->with([
                'unverified_email' => $user->email,
                'unverified_username' => $user->username,
                'success' => 'Account created! We have sent a verification link to your email (' . mask_email($user->email) . '). Please verify your email before signing in.'
            ]);
        }

        // Email verification is disabled: log user in immediately
        Auth::login($user);

        // Send Welcome Email if SMTP & Welcome Email are enabled
        try {
            send_welcome_email($user->email, $user->username, $user->username);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Welcome email trigger error: ' . $e->getMessage());
        }

        // Record initial login
        try {
            UserLogin::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip() ?? '127.0.0.1',
                'user_agent' => substr((string)$request->userAgent(), 0, 255),
                'logged_at' => now(),
            ]);
        } catch (\Throwable $e) {}

        return redirect()->route('dashboard');
    }

    public function verifyNotice(Request $request)
    {
        $email = session('unverified_email');
        if (!$email && Auth::check()) {
            $email = Auth::user()->email;
        }

        $maskedEmail = $email ? mask_email($email) : null;

        return view('pages.verify-email', [
            'email' => $email,
            'maskedEmail' => $maskedEmail,
            'title' => 'Verify Your Email Address'
        ]);
    }

    public function verifyEmail(Request $request, $id, $token)
    {
        $this->ensureEmailVerificationColumns();

        $user = User::find($id);
        if (!$user) {
            return redirect()->route('login')->withErrors(['login' => 'Invalid email verification link.']);
        }

        if ($user->isEmailVerified()) {
            return redirect()->route('login')->with('success', 'Your email address is already verified! You can sign in.');
        }

        if (!$user->email_verification_token || $user->email_verification_token !== $token) {
            return redirect()->route('login')->withErrors(['login' => 'Invalid or expired email verification link. Please request a new one.']);
        }

        $user->markEmailAsVerified();

        // Send Welcome email now that email is confirmed
        try {
            send_welcome_email($user->email, $user->username, $user->username);
        } catch (\Throwable $e) {}

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', '🎉 Your email address has been verified successfully! Welcome to ' . site_name() . '.');
    }

    public function resendVerificationEmail(Request $request)
    {
        $this->ensureEmailVerificationColumns();

        $input = trim((string)$request->input('email', ''));
        if (empty($input) && Auth::check()) {
            $input = Auth::user()->email;
        }
        if (empty($input) && session('unverified_email')) {
            $input = session('unverified_email');
        }

        if (empty($input)) {
            return redirect()->back()->with('error', 'Please enter your account email address to receive a new link.');
        }

        $user = User::where('email', $input)->orWhere('username', $input)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'No account found with username or email: ' . $input);
        }

        if ($user->isEmailVerified()) {
            return redirect()->route('login')->with('success', 'Your email address is already verified. You can sign in.');
        }

        $token = Str::random(48);
        $user->email_verification_token = $token;
        $user->save();

        $verifyUrl = route('verification.verify', ['id' => $user->id, 'token' => $token]);
        send_verification_email($user->email, $user->username, $verifyUrl);

        return redirect()->route('verification.notice')->with([
            'unverified_email' => $user->email,
            'unverified_username' => $user->username,
            'success' => 'A new verification link has been sent to your email (' . mask_email($user->email) . '). Please check your inbox.'
        ]);
    }

    public function googleRedirect()
    {
        $clientId = Setting::get('google_client_id') ?: env('GOOGLE_CLIENT_ID', '421189417454-gbbjstrtcarq898bkjdemholu8te82op.apps.googleusercontent.com');
        $redirectUri = url('/auth/google/callback');
        $url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
        ]);

        return redirect()->away($url);
    }

    public function googleCallback(Request $request)
    {
        $code = $request->query('code');
        if (!$code) {
            return redirect('/')->withErrors(['login' => 'Google authorization failed.']);
        }

        $clientId = Setting::get('google_client_id') ?: env('GOOGLE_CLIENT_ID', '421189417454-gbbjstrtcarq898bkjdemholu8te82op.apps.googleusercontent.com');
        $clientSecret = Setting::get('google_client_secret') ?: env('GOOGLE_CLIENT_SECRET', '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');
        $redirectUri = url('/auth/google/callback');

        try {
            $tokenResp = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            $tokenData = $tokenResp->json();
            $accessToken = $tokenData['access_token'] ?? null;
            if (!$accessToken) {
                return redirect('/')->withErrors(['login' => 'Could not authenticate with Google.']);
            }

            $userResp = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v2/userinfo');
            $googleUser = $userResp->json();
            $email = $googleUser['email'] ?? null;
            if (!$email) {
                return redirect('/')->withErrors(['login' => 'Unable to retrieve Google email.']);
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                $baseUsername = Str::slug(explode('@', $email)[0], '');
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter++;
                }

                $user = User::create([
                    'username' => $username,
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password_hash' => Hash::make(Str::random(24)),
                    'role' => 'user',
                    'points' => 100,
                    'status' => 'active',
                    'referral_code' => strtoupper(Str::random(8)),
                ]);

                Blog::create([
                    'user_id' => $user->id,
                    'slug' => $user->username,
                    'title' => $googleUser['name'] ?? $user->username,
                    'is_main' => 1,
                    'color' => '#ff2bd6',
                    'status' => 'active',
                ]);
            } else {
                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                    $user->save();
                }
            }

            if ($user->status === 'banned') {
                return redirect('/')->withErrors(['login' => 'Account is banned.']);
            }

            Auth::login($user, true);

            try {
                UserLogin::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip() ?? '127.0.0.1',
                    'user_agent' => substr((string)$request->userAgent(), 0, 255),
                    'logged_at' => now(),
                ]);
            } catch (\Throwable $e) {}

            return redirect()->intended('/dashboard');

        } catch (\Throwable $e) {
            return redirect('/')->withErrors(['login' => 'Google authentication error: ' . $e->getMessage()]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}

