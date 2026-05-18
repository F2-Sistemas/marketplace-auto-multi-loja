<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
