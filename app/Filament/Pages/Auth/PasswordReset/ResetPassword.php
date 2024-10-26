<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth\PasswordReset;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\PasswordResetResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Locked;

/**
 * @property Form $form
 */
final class ResetPassword extends SimplePage
{
    use InteractsWithFormActions;
    use WithRateLimiting;

    /**
     * The view for this page.
     */
    protected static string $view = 'filament-panels::pages.auth.password-reset.reset-password';

    /**
     * The user's email address.
     */
    #[Locked]
    public ?string $email = null;

    /**
     * The user's password.
     */
    public ?string $password = '';

    /**
     * The user's password confirmation.
     */
    public ?string $passwordConfirmation = '';

    /**
     * The user's password reset token.
     */
    #[Locked]
    public ?string $token = null;

    /**
     * Mount the page.
     */
    public function mount(Request $request, ?string $email = null, ?string $token = null): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->token = $token ?? $request->query('token');

        $this->form->fill([
            'email' => $email ?? $request->query('email'),
        ]);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(): ?PasswordResetResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)->send();

            return null;
        }

        $data = $this->form->getState();

        $data['email'] = $this->email;
        $data['token'] = $this->token;

        $status = Password::broker(Filament::getAuthPasswordBroker())->reset(
            $data,
            function (CanResetPassword|Model|Authenticatable $user) use ($data) {
                $user->forceFill([
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(60),
                    'email_verified_at' => now(),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            Notification::make()
                ->title(__($status))
                ->success()
                ->send();

            return app(PasswordResetResponse::class);
        }

        Notification::make()
            ->title(__($status))
            ->danger()
            ->send();

        return null;
    }

    /**
     * Get the rate limited notification.
     */
    private function getRateLimitedNotification(TooManyRequestsException $exception): Notification
    {
        return Notification::make()
            ->title(__('filament-panels::pages/auth/password-reset/reset-password.notifications.throttled.title', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]))
            ->body(array_key_exists('body', __('filament-panels::pages/auth/password-reset/reset-password.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/password-reset/reset-password.notifications.throttled.body', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]) : null)
            ->danger();
    }

    /**
     * Get the form for this page.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    /**
     * Get the email form component.
     */
    private function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/password-reset/reset-password.form.email.label'))
            ->disabled()
            ->autofocus();
    }

    /**
     * Get the password form component.
     */
    private function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/password-reset/reset-password.form.password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(PasswordRule::default())
            ->same('passwordConfirmation')
            ->validationAttribute(__('filament-panels::pages/auth/password-reset/reset-password.form.password.validation_attribute'));
    }

    /**
     * Get the password confirmation form component.
     */
    private function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/password-reset/reset-password.form.password_confirmation.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false);
    }

    /**
     * Get the title for this page.
     */
    public function getTitle(): string|Htmlable
    {
        return __('filament-panels::pages/auth/password-reset/reset-password.title');
    }

    /**
     * Get the heading for this page.
     */
    public function getHeading(): string|Htmlable
    {
        return __('filament-panels::pages/auth/password-reset/reset-password.heading');
    }

    /**
     * Get the form actions for this page.
     *
     * @return array<Action | ActionGroup>
     */
    private function getFormActions(): array
    {
        return [
            $this->getResetPasswordFormAction(),
        ];
    }

    /**
     * Get the reset password form action.
     */
    public function getResetPasswordFormAction(): Action
    {
        return Action::make('resetPassword')
            ->label(__('filament-panels::pages/auth/password-reset/reset-password.form.actions.reset.label'))
            ->submit('resetPassword');
    }

    /**
     * Determine if the form actions should be full width.
     */
    private function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
