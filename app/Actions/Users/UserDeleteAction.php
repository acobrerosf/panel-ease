<?php

namespace App\Actions\Users;

use App\Models\User;

class UserDeleteAction
{
    public function handle(User $user): void
    {
        // Update user's email so it's available again.
        $user->email = "{$user->id}@#@{$user->email}";
        $user->save();

        // Delete user.
        $user->delete();
    }
}
