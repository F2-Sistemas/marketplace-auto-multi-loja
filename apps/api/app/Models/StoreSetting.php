<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $store_id
 * @property string $key
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Store $store
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSetting whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreSetting whereValue($value)
 * @mixin \Eloquent
 */
class StoreSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'key',
        'value',
    ];

    /**
     * Get the store that owns the setting.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
