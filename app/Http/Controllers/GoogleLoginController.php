<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            Log::info('Starting Google redirect process');
            return Socialite::driver('google')
                ->with(['prompt' => 'select_account'])
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Failed to redirect to Google', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->with('error', 'Could not connect to Google. Please try again.');
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            Log::info('Google callback received', [
                'has_code' => $request->has('code'),
                'has_state' => $request->has('state'),
                'error' => $request->error ?? 'none'
            ]);

            if ($request->error) {
                throw new \Exception('Google authentication error: ' . $request->error);
            }

            if (!$request->has('code') || !$request->has('state')) {
                throw new \Exception('Invalid callback parameters');
            }

            $googleUser = Socialite::driver('google')->stateless()->user();
            
            Log::info('Google user retrieved', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName()
            ]);

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(\Str::random(24)),
                    'email_verified_at' => now(),
                ]
            );

            Auth::login($user);

            return redirect()->intended(
                str_contains(strtolower($user->email), 'admin') 
                    ? '/admin/dashboard' 
                    : '/dashboard'
            );

        } catch (\Exception $e) {
            Log::error('Google login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect('/login')
                ->with('error', 'Failed to login with Google. Please try again.');
        }
    }
}

