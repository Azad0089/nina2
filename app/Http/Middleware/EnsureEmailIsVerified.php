<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user && $user->role !== 'admin') {
            $verificationEnabled = Setting::get('email_verification_enabled', '0') === '1';
            if ($verificationEnabled && !$user->isEmailVerified()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Your email address is not verified.'], 409);
                }

                return redirect()->route('verification.notice')->with([
                    'unverified_email' => $user->email,
                    'unverified_username' => $user->username,
                    'error' => 'Please verify your email address (' . mask_email($user->email) . ') to access this section.'
                ]);
            }
        }

        return $next($request);
    }
}
