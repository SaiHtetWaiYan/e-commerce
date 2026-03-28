<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showForm(Request $request): View|RedirectResponse
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (! $token || ! $email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Invalid password reset link.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->input('email'))
            ->first();

        if ($record === null) {
            return back()->withErrors(['email' => 'Invalid password reset token.']);
        }

        // Check if token matches
        if (! hash_equals($record->token, hash('sha256', $request->input('token')))) {
            return back()->withErrors(['email' => 'Invalid password reset token.']);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->input('email'))->delete();

            return back()->withErrors(['email' => 'This password reset link has expired. Please request a new one.']);
        }

        $user = User::query()->where('email', $request->input('email'))->first();

        if (! $user instanceof User) {
            return back()->withErrors(['email' => 'No account found with that email address.']);
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        // Delete the used token
        DB::table('password_reset_tokens')->where('email', $request->input('email'))->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset successfully. Please sign in.');
    }
}
