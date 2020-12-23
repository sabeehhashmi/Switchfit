<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Traits\ApiResponse;

class CheckStatus
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check() || Auth::viaRemember()){
            if(Auth::user()->is_disabled == 0 && Auth::user()->is_deleted == 0){
             return $next($request);
         }
         $message = (Auth::user()->is_disabled == 1)?'Sorry! You are disabled from admin':'Sorry! This user is deleted';
         
            $this->setResponse($message, 0, 419, []);
            return response()->json($this->response, $this->status);
       
    }

}
}
