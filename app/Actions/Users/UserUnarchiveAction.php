<?php

namespace App\Actions\Users;

use App\Models\User;

class UserUnarchiveAction
{
    public function __invoke(User $user): void
    {
        $user->archived_at = null;
        $user->save();
    }
}
