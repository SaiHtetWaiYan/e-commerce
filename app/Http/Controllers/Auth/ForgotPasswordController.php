<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $user = User::query()->where('email', $request->input('email'))->first();

        if (! $user instanceof User) {
            // Don't reveal whether a user exists
            return back()->with('status', 'If an account exists with that email, we\'ve sent a password reset link.');
        }

        // Delete any existing tokens for this user
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => hash('sha256', $token),
            'created_at' => now(),
        ]);

        Mail::to($user->email)->send(new ResetPasswordMail($user, $token));

        return back()->with('status', 'If an account exists with that email, we\'ve sent a password reset link.');
    }
}
