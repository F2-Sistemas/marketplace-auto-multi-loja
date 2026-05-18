<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Store;
use App\Models\Vehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class GenerateStoreSitemapJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var int
     */
    private int $storeId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $store = Store::where('id', '=', $this->storeId)
            ->where('status', '=', 'active')
            ->first();

        if ($store === null) {
            return;
        }

        // Get primary domain
        $primaryDomainRecord = $store->domains()
            ->where('status', '=', 'active')
            ->where('is_primary', '=', true)
            ->first();

        if ($primaryDomainRecord === null) {
            return;
        }

        $domain = $primaryDomainRecord->domain;

        // Fetch published vehicles for this store.
        // Important: Since we are running in a background queue worker, the global StoreScope might not be set.
        // So we explicitly query vehicles matching store_id.
        $vehicles = Vehicle::withoutGlobalScopes()
            ->where('store_id', '=', $this->storeId)
            ->where('status', '=', 'published')
            ->get();

        // SEO Rule: If a store has no active vehicles, we avoid publishing an empty or poor quality sitemap.
        if ($vehicles->isEmpty()) {
            return;
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Store home page
        $xml .= '  <url>' . PHP_EOL;
        $xml .= "    <loc>https://{$domain}</loc>" . PHP_EOL;
        $xml .= '    <changefreq>daily</changefreq>' . PHP_EOL;
        $xml .= '    <priority>1.0</priority>' . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;

        // Store vehicle listings
        foreach ($vehicles as $vehicle) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= "    <loc>https://{$domain}/veiculos/{$vehicle->slug}</loc>" . PHP_EOL;
            $xml .= '    <lastmod>' . $vehicle->updated_at->toAtomString() . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
            $xml .= '    <priority>0.8</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        $directory = public_path('sitemaps');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put($directory . "/sitemap-store-{$this->storeId}.xml", $xml);
    }
}
