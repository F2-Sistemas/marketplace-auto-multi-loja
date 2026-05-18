<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
