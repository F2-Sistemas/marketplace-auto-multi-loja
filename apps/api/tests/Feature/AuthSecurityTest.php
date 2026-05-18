<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Mail\PasswordRecoveryMail;
use App\Mail\EmailValidationMail;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /**
     * Test requesting a password reset email link successfully.
     */
    public function testSendResetLinkSuccess(): void
    {
        $user = User::factory()->create([
            'email' => 'test@autohub.com',
            'name' => 'John Doe'
        ]);

        $response = $this->postJson('/api/auth/password/email', [
            'email' => 'test@autohub.com'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success'
            ]);

        // Assert token is in Cache
        $this->assertTrue(Cache::has('password_reset_test@autohub.com'));
        
        // Assert mailable was sent
        Mail::assertSent(PasswordRecoveryMail::class, function (PasswordRecoveryMail $mail) {
            return $mail->hasTo('test@autohub.com');
        });
    }

    /**
     * Test requesting password reset when user does not exist.
     */
    public function testSendResetLinkUserNotFound(): void
    {
        $response = $this->postJson('/api/auth/password/email', [
            'email' => 'notfound@autohub.com'
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error'
            ]);
    }

    /**
     * Test successful password reset execution.
     */
    public function testResetPasswordSuccess(): void
    {
        $user = User::factory()->create([
            'email' => 'test@autohub.com',
            'password' => Hash::make('oldsecret123')
        ]);

        Cache::put('password_reset_test@autohub.com', [
            'token' => '999999',
            'user_id' => $user->id
        ], now()->addMinutes(60));

        $response = $this->postJson('/api/auth/password/reset', [
            'email' => 'test@autohub.com',
            'token' => '999999',
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Senha redefinida com sucesso!'
            ]);

        // Check password is updated in DB
        $user->refresh();
        $this->assertTrue(Hash::check('newsecret123', $user->password));

        // Check cache entry is deleted
        $this->assertFalse(Cache::has('password_reset_test@autohub.com'));
    }

    /**
     * Test password reset with invalid token.
     */
    public function testResetPasswordInvalidToken(): void
    {
        $user = User::factory()->create([
            'email' => 'test@autohub.com'
        ]);

        Cache::put('password_reset_test@autohub.com', [
            'token' => '999999',
            'user_id' => $user->id
        ], now()->addMinutes(60));

        $response = $this->postJson('/api/auth/password/reset', [
            'email' => 'test@autohub.com',
            'token' => 'wrong_token',
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123'
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Código de verificação inválido ou expirado.'
            ]);
    }

    /**
     * Test triggering an email verification email successfully.
     */
    public function testSendVerificationSuccess(): void
    {
        $user = User::factory()->create([
            'email' => 'test@autohub.com'
        ]);

        $response = $this->postJson('/api/auth/email/send-verification', [
            'email' => 'test@autohub.com'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success'
            ]);

        // Assert validation mailable was sent
        Mail::assertSent(EmailValidationMail::class, function (EmailValidationMail $mail) {
            return $mail->hasTo('test@autohub.com');
        });
    }

    /**
     * Test successful email verification confirmation.
     */
    public function testVerifyEmailSuccess(): void
    {
        $user = User::factory()->create([
            'email' => 'test@autohub.com',
            'email_verified_at' => null
        ]);

        $response = $this->postJson('/api/auth/email/verify', [
            'email' => 'test@autohub.com'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'E-mail validado e ativado com sucesso!'
            ]);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }

    /**
     * Test requesting a password reset email link with invalid email parameter (caminho triste).
     */
    public function testSendResetLinkInvalidEmailParameter(): void
    {
        $response = $this->postJson('/api/auth/password/email', [
            'email' => 'invalid-email-format'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test resetting password with mismatched password parameters (caminho triste).
     */
    public function testResetPasswordValidationMismatchedPassword(): void
    {
        $response = $this->postJson('/api/auth/password/reset', [
            'email' => 'test@autohub.com',
            'token' => '123456',
            'password' => 'newsecret123',
            'password_confirmation' => 'differentsecret'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test sending validation link with missing email parameter (caminho triste).
     */
    public function testSendVerificationInvalidEmailParameter(): void
    {
        $response = $this->postJson('/api/auth/email/send-verification', [
            'email' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test email verification with a non-existent user email (caminho triste).
     */
    public function testVerifyEmailUserNotFound(): void
    {
        $response = $this->postJson('/api/auth/email/verify', [
            'email' => 'notfound@autohub.com'
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Nenhum usuário localizado com este endereço de e-mail.'
            ]);
    }
}
