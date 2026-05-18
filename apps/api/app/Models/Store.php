<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Store extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'public_id',
        'name',
        'slug',
        'status',
    ];

    /**
     * Get the domains associated with this store.
     */
    public function domains(): HasMany
    {
        return $this->hasMany(StoreDomain::class);
    }

    /**
     * Get the settings associated with this store.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(StoreSetting::class);
    }

    /**
     * Get the users associated with this store.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'store_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the vehicles associated with this store.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Get the leads associated with this store.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
