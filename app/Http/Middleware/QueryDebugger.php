<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryDebugger
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.debug')) {
            $queryCount = 0;
            
            DB::listen(function ($query) use (&$queryCount) {
                $queryCount++;
            });
            
            $response = $next($request);
            
            // Count total queries
            $queries = DB::getQueryLog();
            $totalQueries = count($queries);
            
            // Log query count if it's excessive
            if ($totalQueries > 50) {
                Log::warning("High query count detected", [
                    'url' => $request->url(),
                    'total_queries' => $totalQueries,
                    'queries' => array_slice($queries, 0, 10) // Log first 10 queries
                ]);
            }
            
            return $response;
        }
        
        return $next($request);
    }
}
