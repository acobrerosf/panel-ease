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

    public function handle(User $user): bool
    {
        return $this->sendResetPasswordAction->handle($user);
    }
}
