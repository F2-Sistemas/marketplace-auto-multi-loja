<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $brand_id
 * @property string $name
 * @property string $slug
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Brand $brand
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Vehicle> $vehicles
 * @property-read int|null $vehicles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleModel whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleModel whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleModel whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class VehicleModel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'type',
    ];

    /**
     * Get the brand that owns the model.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the vehicles associated with this model.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }
}
