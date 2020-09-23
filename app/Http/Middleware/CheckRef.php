<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use App\User;
class CheckRef
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
        $users = User::where('ref_code',$request->Refcode)->get()->first();
        if($users){
            $request->merge(['userexist'=>true, 'userid'=>$users->id]);
        }
        return $next($request);
    }
}
