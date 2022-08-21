<?php

namespace App\Http\Middleware;

use App\Models\Wallet;
use Closure;

class CustomResponseMiddleware
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
        $response = $next($request);

        if(auth()->user()->id ?? null){
            $wallet = Wallet::where(['user_id'=>auth()->user()->id])->first();
            if($wallet){
                $response->header('User-Balance', $wallet->balance);
            }
        }

        return $response;
    }
}
