<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\StoreDomain;
use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Resolve host from header or standard request
        $host = $request->header('X-Store-Host') ?: $request->getHost();

        // 2. Normalize host
        $host = strtolower(trim($host));

        // 3. Skip resolution if host matches portal central API or administration domains
        $portalDomains = [
            config('app.domains.api', 'api.rederevenda.com'),
            config('app.domains.portal', 'rederevenda.com'),
            config('app.domains.backoffice', 'admin.rederevenda.com'),
        ];

        if (in_array($host, $portalDomains, true)) {
            return $next($request);
        }

        // 4. Resolve domain with Redis caching
        $storeId = Cache::remember("store:domain:{$host}", 86400, function () use ($host) {
            $domainRecord = StoreDomain::where('domain', $host)
                ->where('status', 'active')
                ->first();

            return $domainRecord ? $domainRecord->store_id : null;
        });

        // 5. If store_id was found, resolve and set Store model in TenantManager
        if ($storeId !== null) {
            $store = \App\Models\Store::find($storeId);

            if ($store !== null) {
                app(TenantManager::class)->setStore($store);
            }
        }

        return $next($request);
    }
}
