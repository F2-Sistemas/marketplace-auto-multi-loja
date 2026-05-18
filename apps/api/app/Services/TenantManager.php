<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Store;

class TenantManager
{
    /**
     * @var int|null
     */
    private ?int $storeId = null;

    /**
     * @var Store|null
     */
    private ?Store $store = null;

    /**
     * Set the current resolved store ID.
     */
    public function setStoreId(int $storeId): void
    {
        $this->storeId = $storeId;
    }

    /**
     * Get the current resolved store ID.
     */
    public function getStoreId(): ?int
    {
        return $this->storeId;
    }

    /**
     * Set the current resolved store model.
     */
    public function setStore(Store $store): void
    {
        $this->store = $store;
        $this->storeId = $store->id;
    }

    /**
     * Get the current resolved store model.
     */
    public function getStore(): ?Store
    {
        return $this->store;
    }

    /**
     * Check if a store has been resolved.
     */
    public function hasStore(): bool
    {
        return $this->storeId !== null;
    }

    /**
     * Reset the tenant context (useful in tests or background queues).
     */
    public function clear(): void
    {
        $this->storeId = null;
        $this->store = null;
    }
}
