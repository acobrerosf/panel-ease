<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;

final readonly class UserUnarchiveAction
{
    /**
     * Handle the action.
     */
    public function handle(User $user): void
    {
        $user->archived_at = null;
        $user->save();
    }
}
