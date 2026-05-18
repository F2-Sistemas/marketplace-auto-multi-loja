<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Mail\PasswordRecoveryMail;
use App\Mail\EmailValidationMail;

class AuthController extends Controller
{
    /**
     * Send password reset code email.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nenhum usuário localizado com este endereço de e-mail.'
            ], 404);
        }

        // Generate 6-digit verification code
        $token = (string) rand(100000, 999999);

        // Store token in cache for 60 minutes
        Cache::put('password_reset_' . $email, [
            'token' => $token,
            'user_id' => $user->id
        ], now()->addMinutes(60));

        // Send email via Mailpit SMTP
        Mail::to($email)->send(new PasswordRecoveryMail(
            $user->name,
            $token,
            'http://localhost:3000/auth/reset?email=' . urlencode($email) . '&token=' . $token
        ));

        return response()->json([
            'status' => 'success',
            'message' => 'E-mail de recuperação de senha enviado com sucesso via SMTP!'
        ]);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $email = $request->input('email');
        $token = $request->input('token');
        $password = $request->input('password');

        $cached = Cache::get('password_reset_' . $email);

        if (!$cached || $cached['token'] !== $token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Código de verificação inválido ou expirado.'
            ], 400);
        }

        $user = User::find($cached['user_id']);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuário não localizado.'
            ], 404);
        }

        // Update password
        $user->password = Hash::make($password);
        $user->save();

        // Clear cache entry
        Cache::forget('password_reset_' . $email);

        return response()->json([
            'status' => 'success',
            'message' => 'Senha redefinida com sucesso!'
        ]);
    }

    /**
     * Send email verification link.
     */
    public function sendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nenhum usuário localizado com este endereço de e-mail.'
            ], 404);
        }

        // Send email via Mailpit SMTP
        Mail::to($email)->send(new EmailValidationMail(
            $user->name,
            'http://localhost:3000/auth/verify?email=' . urlencode($email)
        ));

        return response()->json([
            'status' => 'success',
            'message' => 'E-mail de validação enviado com sucesso via SMTP!'
        ]);
    }

    /**
     * Mark the user's email as verified.
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nenhum usuário localizado com este endereço de e-mail.'
            ], 404);
        }

        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'E-mail validado e ativado com sucesso!'
        ]);
    }
}
