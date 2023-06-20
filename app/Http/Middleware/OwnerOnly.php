<?php

namespace App\Http\Middleware;

use Closure;

class OwnerOnly
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! tenant()->isInAdminTeam()) {
            abort(403);
        }

        return $next($request);
    }
}
