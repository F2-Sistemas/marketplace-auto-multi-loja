<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Scopes\StoreScope;
use App\Models\Store;
use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToStore
{
    /**
     * Boot the trait to apply the StoreScope global scope and auto-fill store_id on save.
     */
    public static function bootBelongsToStore(): void
    {
        static::addGlobalScope(new StoreScope());

        static::creating(function ($model) {
            $tenantManager = app(TenantManager::class);

            if ($model->store_id === null && $tenantManager->hasStore()) {
                $model->store_id = $tenantManager->getStoreId();
            }
        });
    }

    /**
     * Get the store that owns the model.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
