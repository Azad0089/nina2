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

        $user = User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
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

        Auth::login($user);

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

    public function googleRedirect()
    {
        $clientId = Setting::get('google_client_id', '421189417454-gbbjstrtcarq898bkjdemholu8te82op.apps.googleusercontent.com');
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

        $clientId = Setting::get('google_client_id', '421189417454-gbbjstrtcarq898bkjdemholu8te82op.apps.googleusercontent.com');
        $clientSecret = Setting::get('google_client_secret', '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');
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
            }

            if ($user->status === 'banned') {
                return redirect('/')->withErrors(['login' => 'Account is banned.']);
            }

            Auth::login($user, true);
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

