<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;

final readonly class UserDeleteAction
{
    /**
     * Handle the action.
     */
    public function handle(User $user): void
    {
        // Update user's email so it's available again.
        $user->email = "{$user->id}@#@{$user->email}";
        $user->save();

        // Delete user.
        $user->delete();
    }
}
