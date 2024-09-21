<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Exception;

final readonly class SendResetPasswordAction
{
    /**
     * Handle the action.
     */
    public function handle(User $user): bool
    {
        try {
            $token = app('auth.password.broker')->createToken($user);
            $notification = new \Filament\Notifications\Auth\ResetPassword($token);
            $notification->url = \Filament\Facades\Filament::getResetPasswordUrl($token, $user);
            $user->notify($notification);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
