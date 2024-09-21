<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Actions\Auth\SendResetPasswordAction;
use App\Models\User;

final readonly class UserInviteAction
{
    /**
     * Constructor.
     */
    public function __construct(
        private SendResetPasswordAction $sendResetPasswordAction
    ) {}

    /**
     * Handle the action.
     */
    public function handle(User $user): bool
    {
        return $this->sendResetPasswordAction->handle($user);
    }
}
