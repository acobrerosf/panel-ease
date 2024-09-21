<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property-read Collection<int, User> $users
 */
final class UserType extends Model
{
    /**
     * Super Admin (DB Value)
     *
     * @var int
     */
    public const SUPER_ADMIN = 1;

    /**
     * Administrator (DB Value)
     *
     * @var int
     */
    public const ADMINISTRATOR = 2;

    /**
     * Get the users.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get user type name translated.
     */
    public function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => __($value)
        );
    }
}
