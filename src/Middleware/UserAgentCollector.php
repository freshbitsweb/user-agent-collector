<?php

namespace Freshbitsweb\UserAgentCollector\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class UserAgentCollector
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        // If the request doesn't contain any cookies, it means device is visiting us for the first time
        // We avoid requests with prefixes like /api, /admin, etc.
        if (empty($request->cookie()) && empty(Route::current()->getPrefix())) {
            // Developer may not have run the migrations
            if (Schema::hasTable('visitor_user_agents')) {
                DB::table('visitor_user_agents')->insert([
                    'user_agent' => $request->header('user-agent'),
                    'ip_address' => $request->ip(),
                    'created_at' => new Carbon,
                ]);
            }
        }
    }
}
