<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\CacheHelper;

class ClearCacheMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $cacheType = 'all'): Response
    {
        $response = $next($request);

        // Only clear cache for successful POST, PUT, PATCH, DELETE requests
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']) && $response->getStatusCode() < 400) {
            switch ($cacheType) {
                case 'settings':
                    CacheHelper::clearSettingsCache();
                    break;
                case 'categories':
                    CacheHelper::clearCategoriesCache();
                    break;
                case 'brands':
                    CacheHelper::clearBrandsCache();
                    break;
                case 'products':
                    CacheHelper::clearProductsCache();
                    break;
                case 'sliders':
                    CacheHelper::clearSlidersCache();
                    break;
                case 'all':
                default:
                    CacheHelper::clearAllGlobalCache();
                    break;
            }
        }

        return $response;
    }
}
