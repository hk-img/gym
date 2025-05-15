<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Carbon\Carbon;  
use Mail;
class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        // Generate a reset token
        $token = \Str::random(64);

        // Store the token in the password_resets table
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => bcrypt($token),
                'created_at' => Carbon::now()
            ]
        );

        // Create reset URL with plain token
        $resetUrl = url(route('admin.password.reset', ['token' => $token, 'email' => $request->email], false));

        // Send email
        Mail::send('emails.reset-password', [
            'email' => $request->email,
            'resetUrl' => $resetUrl,
            'messageText' => 'You requested a password reset. Click the button below to reset your password.'
        ], function ($mail) use ($request) {
            $mail->to($request->email)
                    ->subject('Password Reset Request - GYM');
        });

        return back()->with('status', 'Password reset link has been sent to your email.');
    }

}
