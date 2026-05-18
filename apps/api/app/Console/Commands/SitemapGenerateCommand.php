<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\GeneratePortalSitemapJob;
use App\Jobs\GenerateStoreSitemapJob;
use App\Models\Store;
use Illuminate\Console\Command;

class SitemapGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate 
                            {--scope=all : The generation scope: "all", "portal", or "stores"}
                            {--store= : ID of a specific store to generate}
                            {--stores= : Comma-separated list of store IDs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate portal and dynamic store sitemaps asynchronously via Redis queue';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $scope = $this->option('scope');
        $storeId = $this->option('store');
        $storeIdsString = $this->option('stores');

        // Case 1: Specific store ID
        if ($storeId !== null) {
            $this->info("Despachando sitemap para a loja ID: {$storeId}...");
            GenerateStoreSitemapJob::dispatch((int) $storeId);
            $this->info("Sucesso!");

            return 0;
        }

        // Case 2: Comma-separated list of store IDs
        if ($storeIdsString !== null) {
            $ids = array_filter(explode(',', (string) $storeIdsString));
            $this->info("Despachando sitemaps para as lojas: " . implode(', ', $ids) . "...");

            foreach ($ids as $id) {
                GenerateStoreSitemapJob::dispatch((int) $id);
            }
            $this->info("Sucesso!");

            return 0;
        }

        // Case 3: Standard Scope handling
        if ($scope === 'portal' || $scope === 'all') {
            $this->info("Despachando geração do sitemap central do Portal...");
            GeneratePortalSitemapJob::dispatch();
        }

        if ($scope === 'stores' || $scope === 'all') {
            $this->info("Despachando geração de sitemaps para todas as lojas ativas...");
            $activeStores = Store::where('status', '=', 'active')->get();

            foreach ($activeStores as $store) {
                GenerateStoreSitemapJob::dispatch((int) $store->id);
            }

            $this->info("Geração agendada para {$activeStores->count()} lojas!");
        }

        $this->info("Processamento de sitemaps enviado com sucesso para a fila Redis!");

        return 0;
    }
}
