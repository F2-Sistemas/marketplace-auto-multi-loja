<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $vehicle_id
 * @property string $path
 * @property bool $is_featured
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereVehicleId($value)
 * @mixin \Eloquent
 */
class VehicleImage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'path',
        'is_featured',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'image_url',
    ];

    /**
     * Get the full URL for the image.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->path)) {
            return 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?q=80&w=800&auto=format&fit=crop';
        }

        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return $this->path;
        }

        return \Storage::disk('s3-uploads')->url($this->path);
    }

    /**
     * Get the vehicle that owns this image.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
