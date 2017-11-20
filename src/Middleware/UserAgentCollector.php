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

    /**
     * Makes an entry in the database after response is sent to the client
     *
     * @return void
     */
    public function terminate($request, $response)
    {
        if ($this->requestShouldBeRecorded($request)) {
            DB::table('visitor_user_agents')->insert([
                'user_agent' => $request->header('user-agent'),
                'ip_address' => $request->ip(),
                'request_url' => $request->url(),
                'created_at' => new Carbon,
            ]);
        }
    }

    /**
     * Decides weather user agent of the request should be recorded or not
     *
     * @param \Illuminate\Http\Request
     * @return boolean
     */
    protected function requestShouldBeRecorded($request)
    {
        return
            // If the request doesn't contain any cookies, it means device is visiting us for the first time
            empty($request->cookie()) &&

            // We avoid requests with prefixes like /api, /admin, etc.
            ! empty(Route::current()) &&
            empty(Route::current()->getPrefix()) &&

            // Developer may not have run the migrations
            Schema::hasTable('visitor_user_agents') &&

            // Some requests like webhook/ipn do not contain user agents
            ! empty($request->header('user-agent'))
        ;
    }
}
