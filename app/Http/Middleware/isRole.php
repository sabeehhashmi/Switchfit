<?php

namespace App\Http\Middleware;

use Closure;

class isRole
{

    /**
     * @Description Handle an incoming request.
     *              Checking by Roles (super admin,gym owner, trainer)
     * @param $request
     * @param Closure $next
     * @param $role
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     * @Author Khuram Qadeer.
     */
    public function handle($request, Closure $next,$role)
    {
        if (isSuperAdmin()){
            return $next($request);
        }else if ( ($role=='super_admin' && isSuperAdmin())
            || ($role=='gym_owner' && isGymOwner())
            || ($role=='trainer' && isTrainer())
        ){
            return $next($request);
        }
        return redirect(route('dashboard'));

    }
}
