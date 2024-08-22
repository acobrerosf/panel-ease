<?php

namespace App\Actions\Users;

use App\Actions\Auth\SendResetPasswordAction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserCreateAction
{
    public function __construct(
        private SendResetPasswordAction $sendResetPasswordAction
    )
    {
        
    }

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
            $this->sendResetPasswordAction->__invoke($user);

            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $user;
    }
}
