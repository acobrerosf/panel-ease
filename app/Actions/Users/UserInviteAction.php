<?php

namespace App\Actions\Users;

use App\Models\User;
use Exception;

class UserInviteAction
{
    public function __invoke(User $user): bool
    {
        try {
            $token = app('auth.password.broker')->createToken($user);
            $notification = new \Filament\Notifications\Auth\ResetPassword($token);
            $notification->url = \Filament\Facades\Filament::getResetPasswordUrl($token, $user);
            $user->notify($notification);
        }
        catch (Exception $e) {
            return false;
        }

        return true;
    }
}
