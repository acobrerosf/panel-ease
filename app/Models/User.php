<?php

namespace App\Models;

use App\Enums\PanelEnums;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * {@inheritdoc}
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'archived_at' => 'datetime',
        ];
    }

    /**
     * Get the type.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    /**
     * Get if the user is archived.
     */
    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive(Builder $query): void
    {
        $query->whereNull('archived_at');
    }

    /**
     * Scope a query to only include archived users.
     */
    public function scopeArchived(Builder $query): void
    {
        $query->whereNotNull('archived_at');
    }

    /**
     * Get if the user is active.
     */
    public function isActive(): bool
    {
        return ! $this->isArchived();
    }

    /**
     * {@inheritdoc}
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->hasVerifiedEmail() || ! $this->isActive()) {
            return false;
        }

        switch ($panel->getId()) {
            case PanelEnums::Admin->value:
                return in_array($this->type_id, [UserType::FULL_ADMINISTRATOR, UserType::ADMINISTRATOR]);
        }

        return false;
    }
}
