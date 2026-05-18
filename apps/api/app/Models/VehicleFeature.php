<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $vehicle_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleFeature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleFeature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleFeature whereVehicleId($value)
 * @mixin \Eloquent
 */
class VehicleFeature extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'name',
    ];

    /**
     * Get the vehicle that owns this feature.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
