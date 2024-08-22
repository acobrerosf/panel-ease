<?php

namespace App\Actions\Users;

use App\Actions\Auth\SendResetPasswordAction;
use App\Models\User;

class UserInviteAction
{
    public function __construct(
        private SendResetPasswordAction $sendResetPasswordAction
    )
    {
        
    }

    public function __invoke(User $user): bool
    {
        return $this->sendResetPasswordAction->__invoke($user);
    }
}
