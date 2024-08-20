<?php

namespace App\Actions\Users;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserCreateAction
{
    public function __invoke(array $data): User
    {
        DB::beginTransaction();

        try {
            // Create user.
            $user = new User();
            $user->fill($data);
            $user->password = Str::random(10);
            $user->save();

            // Send reset password notification.
            $token = app('auth.password.broker')->createToken($user);
            $notification = new \Filament\Notifications\Auth\ResetPassword($token);
            $notification->url = \Filament\Facades\Filament::getResetPasswordUrl($token, $user);
            $user->notify($notification);

            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $user;
    }
}
