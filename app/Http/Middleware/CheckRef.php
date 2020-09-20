<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

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
        $users = DB::select('select ref_code,id from users where ref_code = ?', [$request->Refcode]);
        if(count($users)>0){
            $request->merge(['userexist'=>true, 'userid'=>$users[0]->id]);
        }
        return $next($request);
    }
}
