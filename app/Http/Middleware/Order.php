<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use App\User;
use Illuminate\Http\Request;

class Order
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
        $orcoins = $request->orcoins;
        $header = $request->header('Authorization', '');
        if (Str::startsWith($header, 'Bearer ')) {
            $token = Str::substr($header, 7);
        }
        $dat = User::where('api_token', $token)->get(['id', 'tot_coins']);
        if($dat[0]->tot_coins<$orcoins){
            return response()->json(["status"=>false, "msg"=>"Earn Some Coins"], 200);
        }
        return $next($request);
    }
}
