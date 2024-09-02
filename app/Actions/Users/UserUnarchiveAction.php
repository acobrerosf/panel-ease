<?php

namespace App\Actions\Users;

use App\Models\User;

class UserUnarchiveAction
{
    public function handle(User $user): void
    {
        $user->archived_at = null;
        $user->save();
    }
}
