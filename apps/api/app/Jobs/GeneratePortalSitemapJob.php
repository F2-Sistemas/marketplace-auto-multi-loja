<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Vehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class GeneratePortalSitemapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Retrieve published vehicles with active stores
        $vehicles = Vehicle::where('status', '=', 'published')
            ->whereHas('store', function ($query) {
                $query->where('status', '=', 'active');
            })
            ->get();

        $portalDomain = config('app.domains.portal', 'rederevenda.com');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Add home page
        $xml .= '  <url>' . PHP_EOL;
        $xml .= "    <loc>https://{$portalDomain}</loc>" . PHP_EOL;
        $xml .= '    <changefreq>daily</changefreq>' . PHP_EOL;
        $xml .= '    <priority>1.0</priority>' . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;

        // Add each published vehicle link on the portal
        foreach ($vehicles as $vehicle) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= "    <loc>https://{$portalDomain}/veiculos/{$vehicle->slug}</loc>" . PHP_EOL;
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

        File::put($directory . '/sitemap-portal.xml', $xml);
    }
}
