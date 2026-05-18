<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $store_id
 * @property int $brand_id
 * @property int $vehicle_model_id
 * @property int $city_id
 * @property string $title
 * @property string $slug
 * @property string|null $version
 * @property string|null $description
 * @property float $price
 * @property int $year_manufacture
 * @property int $year_model
 * @property int $mileage
 * @property string|null $color
 * @property string|null $transmission
 * @property string|null $fuel
 * @property string $status
 * @property int $views_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Brand $brand
 * @property-read City $city
 * @property-read VehicleImage|null $featuredImage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, VehicleFeature> $features
 * @property-read int|null $features_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, VehicleImage> $images
 * @property-read int|null $images_count
 * @property-read VehicleModel $model
 * @property-read Store $store
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereFuel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereMileage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereTransmission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereVehicleModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereViewsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereYearManufacture($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereYearModel($value)
 * @mixin \Eloquent
 */
class Vehicle extends Model
{
    use BelongsToStore;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'brand_id',
        'vehicle_model_id',
        'city_id',
        'title',
        'slug',
        'version',
        'description',
        'price',
        'year_manufacture',
        'year_model',
        'mileage',
        'color',
        'transmission',
        'fuel',
        'status',
        'views_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'year_manufacture' => 'integer',
        'year_model' => 'integer',
        'mileage' => 'integer',
        'views_count' => 'integer',
    ];

    /**
     * Get the brand of the vehicle.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the model of the vehicle.
     */
    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }

    /**
     * Get the city where the vehicle is located.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the images of the vehicle.
     */
    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    /**
     * Get the featured image of the vehicle.
     */
    public function featuredImage()
    {
        return $this->hasOne(VehicleImage::class)->where('is_featured', true);
    }

    /**
     * Get the features of the vehicle.
     */
    public function features(): HasMany
    {
        return $this->hasMany(VehicleFeature::class);
    }
}
