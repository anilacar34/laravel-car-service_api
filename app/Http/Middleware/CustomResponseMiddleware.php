<?php

namespace App\Http\Middleware;

use App\Models\Wallet;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

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

        $cachedWallet = Redis::get('wallet_' . auth()->user()->id);

        if(isset($cachedWallet)) {
            $walletBalance = $cachedWallet;
        }else{
            if(auth()->user()->id ?? null){
                $wallet = Wallet::where(['user_id'=>auth()->user()->id])->first();
                if($wallet){
                    $walletBalance = $wallet->balance;
                    Redis::set('wallet_' . auth()->user()->id, $wallet->balance);
                }
            }
        }

        return $response->header('User-Balance', $walletBalance);
    }
}
