<?php

namespace App\Http\Controllers\Func;

use App\User;
use App\redeemcode;
use App\notification;
use App\Buycoin;
use App\order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\AllFunction;
use Exception;

class UserData extends Controller
{
    public function addCoins(Request $request)
    {
        $addingcoin = new AllFunction();
        $data = $addingcoin->addCoins($request->coins, $request->user()->id);
        if($data == 1){
            return response()->json(['status'=>true, "msg"=>"Coins Added"], 200);
        }else{
            return response()->json(['status'=>false, "msg"=>"Somthing Went Wrong"], 200);
        }
        
    }


    public function generateRedeemCoin(Request $request)
    {
        $newRdmNum = new AllFunction();
        $getRedeemCoin = $newRdmNum->random_strings(10);
        if($getRedeemCoin){
            $data =new redeemcode();
            $data->code_redeem = $getRedeemCoin;
            $data->coins = $request->coins;
            if($data->save()){
                return response()->json(['status'=>true, 'code'=>$getRedeemCoin], 200);
            }
        }else{
            return response()->json(['status'=>false, 'msg'=>'Some problem occur ! Try Again!'], 200);
        }
    }

    public function applyredeem(Request $request)
    { 
        try {
            $getCode = $request->code;
            $results = redeemcode::where('used',0)->where('code_redeem',$getCode)->get()->first();
            $coinstobeadd = $results->coins;
            $hey = 'Congratulation '.$request->user()->name.' We have added ' . $coinstobeadd . ' Redeem Coins in your account';
            if($results){
                $addingcoin = new AllFunction();
                $data = redeemcode::where('code_redeem',$getCode)->get()->first();
                $data->used = 1;
                $affected=$data->update();
                if($affected == 1){
                    $added = $addingcoin->addCoins($coinstobeadd, $request->user()->id);
                    if($added){
                        $msg = $addingcoin->sendNotification($hey, $request->user()->id);
                        return response()->json(['status'=>true, 'msg'=>'Redeem Successfull and Coin is added to you account'], 200);
                    }else{
                        return response()->json(['status'=>false, 'msg'=>'Something went wrong.'], 200);
                    }
                }
            }
        } catch (Exception $th) {
            return response()->json(['status'=>false, 'msg'=>'Sorry! Redeem Code has been used.'], 200);
        }
    }

    public function markseen(Request $request)
    {
        $done = notification::where('u_id',$request->user()->id)->update(array('seen'=>1));
        return $done;
    }

    public function applyrefCode(Request $request)
    {
        $ref_Code = $request->Refcode;
        if($request->user()->ref_by == null and $ref_Code != $request->user()->ref_code){
            try {
                $users = User::where('ref_code',$ref_Code)->get()->first();
                if($users){
                    $data = User::where('id',$request->user()->id)->get()->first();
                    $data->ref_by = $ref_Code;
                    if($data->update()){
                        $addingcoin = new AllFunction();
                        $add = $addingcoin->addCoins(30, $request->user()->id);
                        if($add){
                            $notify = $addingcoin->sendNotification('Refered coins has been added', $request->user()->id);
                            return response()->json(['status'=>true, 'msg'=>'Coin Added to your account'], 200);
                        } 
                    }
                    
                }else{
                    return response()->json(['status'=>false, 'msg'=>'Please Check your code again'], 200);
                }
            } catch (Exception $th) {
                return response()->json(['status'=>false, 'msg'=>'Please Check your code again'], 200);
            }
        }else{
            return response()->json(['status'=>false, 'msg'=>'Sorry! Redeem Code has been used.'], 404);
        }
    }

    public function buyCoins(Request $request)
    {
        
        $data = ["status"=>true, "msg"=>"After Verification Coins will be added to you account"];
        try {
            $buy = new Buycoin();

            $buy->tranfer_id = $request->transid;
            $buy->user_id = $request->user()->id;
            $buy->coins = $request->coin;
            $buy->price = $request->price;
            $buy->place = $request->place;
            if($buy->save()){
                return response()->json($data, 200);
            }
        } catch (Exception $th) {
            return response()->json(["status"=>false, "msg"=>"Somthing went wrong! Mail us to luvprogramming@gmail.com"], 200);
        }
    }

    public function order(Request $request)
    {
        $data = ['status'=>true, 'msg'=>'Order Registered'];
        try {
            $or = new order();
            $or->user_id = $request->user()->id;
            $or->link = $request->link;
            $or->action = $request->act;
            $or->quantity = $request->quantity;
            $or->coins = $request->orcoins;

            if($or->save()){
                $urcoin = User::where('id',$request->user()->id)->get()->first();
                $urcoin->tot_coins = $urcoin->tot_coins - $request->orcoins;
                if($urcoin->update()){
                    $notify = new AllFunction();
                    $send = $notify->sendNotification('Your Order has been Registered Successfully', $request->user()->id);
                    if($send){
                        return response()->json($data, 200);
                    }
                }
            } 
        } catch (Exception $th) {
            return $th->getmessage();
        }
    }

    public function getAllOrder()
    {
        $users = order::where('done',0)->get()->toArray();
        return $users;
    }

    public function OrderCompleted(Request $request)
    {
        $users = order::where('id',$request->oid)->where('done',0)->get()->first();
        if($users){
          $users->done = 1;
          if($users->update()){
                $notify = new AllFunction();
                $send = $notify->sendNotification('Your Order is Completed', $users->id);
                return response()->json(['status'=>true, 'msg'=>'Order Completed'], 200);
        }
    }else{
        return response()->json(['status'=>false, 'msg'=>'Order Not Found'], 200);
    }
}

    public function showNotifi(Request $request)
    {
        $users = notification::where('u_id', $request->user()->id)->orderBy('id','desc')->paginate(10);
        return response()->json($users, 200);
    }    
    
    public function numberofNotif(Request $request)
    {
       $users = notification::where('seen',0)->where('u_id',$request->user()->id)->get()->toArray();
       return response()->json(["Num"=>count($users)], 200);
    }
    
    public function getHistory(Request $request)
    {
        $users = order::where('user_id', $request->user()->id)->orderBy('id','desc')->paginate(10);
        return response()->json($users, 200);
    }

}
