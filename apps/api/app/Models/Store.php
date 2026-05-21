<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $public_id
 * @property string $name
 * @property string $slug
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StoreDomain> $domains
 * @property-read int|null $domains_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Lead> $leads
 * @property-read int|null $leads_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StoreSetting> $settings
 * @property-read int|null $settings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Vehicle> $vehicles
 * @property-read int|null $vehicles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Store whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Store extends Model
{
    /**
     * Attributes appended to the JSON representation.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'logo_url',
        'whatsapp_number',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'public_id',
        'name',
        'slug',
        'status',
    ];

    /**
     * Get the domains associated with this store.
     */
    public function domains(): HasMany
    {
        return $this->hasMany(StoreDomain::class);
    }

    /**
     * Get the settings associated with this store.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(StoreSetting::class);
    }

    /**
     * Get the users associated with this store.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'store_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the vehicles associated with this store.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Get the leads associated with this store.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get the store logo URL from settings.
     */
    public function getLogoUrlAttribute(): string
    {
        $logoUrl = $this->resolveSettingValue('logo_url');
        if ($logoUrl !== '') {
            return $logoUrl;
        }

        return 'https://api.rederevenda.com/images/default-logo.png';
    }

    /**
     * Get the store WhatsApp number from settings.
     */
    public function getWhatsappNumberAttribute(): string
    {
        $whatsappNumber = $this->resolveSettingValue('whatsapp_number');
        if ($whatsappNumber !== '') {
            return $whatsappNumber;
        }

        return '5584999999999';
    }

    /**
     * Resolve a store setting value with a consistent fallback.
     */
    private function resolveSettingValue(string $key): string
    {
        if ($this->relationLoaded('settings')) {
            $setting = $this->settings->firstWhere('key', $key);
            if ($setting !== null && filled($setting->value)) {
                return (string) $setting->value;
            }
        }

        $setting = $this->settings()->where('key', $key)->first();
        if ($setting === null || !filled($setting->value)) {
            return '';
        }

        return (string) $setting->value;
    }
}
