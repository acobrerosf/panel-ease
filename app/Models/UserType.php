<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserType extends Model
{
    /**
     * Full Administrator (DB Value)
     * 
     * @var int
     */
    public const FULL_ADMINISTRATOR = 1;

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
