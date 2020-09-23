<?php

namespace App\Http\Controllers\Func;

use App\Buycoin;
use App\website;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\AllFunction;

class payments extends Controller
{
    public function getAllPayments()
    {
        $results = Buycoin::all();
        return $results;
    }

    public function markPaymentDone($buyid)
    {
        $results = Buycoin::where('id',$buyid)->get()->first();
        if($results){
            $buyadd = new AllFunction();
            $done = $buyadd->addCoins($results->coins, $results->user_id);
            if($done){
                $notify = $buyadd->sendNotification(''.$results->coins.' coins you buyed has been added to your account', $results->user_id);
                if($results->delete()){
                    return response()->json(['status'=>true, 'msg'=>'WorkCompleted and  Deleted'], 200);
                }
            }else{
                return response()->json(['status'=>false, 'msg'=>'Try Again!.'], 200);
            }
        }else{
            return response()->json(['status'=>false, 'msg'=>'You entred wrong credential.'], 200);
        }
    }

    public function addwebsite(Request $req)
    {   
        //http://127.0.0.1:8000/api/markdone
        $db = new website();
        $db->weblink = $req->weblink;
        $db->time = $req->time;
        $db->coins = $req->coins;       
        if($db->save()){
            return response()->json(['status'=>true, 'msg'=>'Website link has been added'], 200);
        }else{
            return response()->json(['status'=>false, 'msg'=>'Try Again!'], 200);
        }

    }

    public function showWeb()
    {
        $results = website::all();
        return $results;
    }

}
