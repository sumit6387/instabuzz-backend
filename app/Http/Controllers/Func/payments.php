<?php

namespace App\Http\Controllers\Func;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\AllFunction;

class payments extends Controller
{
    public function getAllPayments()
    {
        $results = DB::select('select * from buycoins');
        return $results;
    }

    public function markPaymentDone($buyid)
    {
        $results = DB::select('select * from buycoins where id = :id', ['id' => $buyid]);
        $buyadd = new AllFunction();
        $done = $buyadd->addCoins($results[0]->coins, $results[0]->user_id);
        if($done){
            $notify = $buyadd->sendNotification(''.$results[0]->coins.' coins you buyed has been added to your account', $results[0]->user_id);
            $deleted = DB::delete('delete from buycoins where id=?',[$buyid]);
            if($deleted == 1){
                return response()->json(['status'=>true, 'msg'=>'WorkCompleted and Deleted'], 200);
            }
        }
    }

    public function addwebsite(Request $req)
    {   
        //http://127.0.0.1:8000/api/markdone
        $db = DB::insert('insert into websites (weblink, time,coins) values (?, ?, ?)', 
        [$req->weblink, $req->time, $req->coins]);
        if($db == 1){
            return response()->json(['status'=>true, 'msg'=>'Website link has been added'], 200);
        }

    }

    public function showWeb()
    {
        $results = DB::select('select * from websites');
        return $results;
    }

}
