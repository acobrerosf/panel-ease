<?php

namespace App\Actions\Users;

use App\Models\User;

class UserArchiveAction
{
    public function handle(User $user): void
    {
        $user->archived_at = now();
        $user->save();
    }
}
