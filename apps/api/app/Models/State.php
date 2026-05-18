<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'uf',
    ];

    /**
     * Get the cities in this state.
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
