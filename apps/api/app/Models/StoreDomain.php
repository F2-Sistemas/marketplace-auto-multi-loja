<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $store_id
 * @property string $domain
 * @property string $type
 * @property bool $is_primary
 * @property bool $is_verified
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Store $store
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDomain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDomain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDomain query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDomain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDomain whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDomain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDomain whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDomain whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDomain whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDomain whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDomain whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDomain whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDomain whereVerifiedAt($value)
 * @mixin \Eloquent
 */
class StoreDomain extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'domain',
        'type',
        'is_primary',
        'is_verified',
        'verified_at',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the store that owns the domain.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
