<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Actions\Auth\SendResetPasswordAction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class UserCreateAction
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
    public function handle(array $data): User
    {
        DB::beginTransaction();

        try {
            // Create user.
            $user = new User;
            $user->fill($data);
            $user->password = Str::random(10);
            $user->save();

            // Send reset password notification.
            $this->sendResetPasswordAction->handle($user);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $user;
    }
}
