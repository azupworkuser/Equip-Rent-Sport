<?php

namespace App\Http\Middleware;

class TeamsPermission
{
    /**
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if (\Auth::guard('sanctum')->user() !== null) {
            setPermissionsTeamId(\Auth::guard('sanctum')->user()->getTenant());
        }

        return $next($request);
    }
}
