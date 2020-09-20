<?php

namespace App\Http\Controllers\Func;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\AllFunction;
use Illuminate\Support\Facades\DB;
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
        DB::insert('insert into redeemcode (code_redeem, coins) values (?, ?)', [$getRedeemCoin, $request->coins]);
        return response()->json(['status'=>true, 'code'=>$getRedeemCoin], 200);
    }

    public function applyredeem(Request $request)
    { 
        try {
            $getCode = $request->code;
            $results = DB::select('select * from redeemcode where used = :use && code_redeem = :code', ['use' => 0, 'code'=>$getCode]);
            $coinstobeadd = $results[0]->coins;
            $hey = 'Congratulation '.$request->user()->name.' We have added ' . $coinstobeadd . ' Redeem Coins in your account';
            if(count($results) > 0){
                $addingcoin = new AllFunction();
                $affected = DB::update('update redeemcode set used = 1 where code_redeem = ?', [$getCode]);
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
        $done = DB::update('update notification set seen = 1 where u_id = ?', [$request->user()->id]);
        return $done;
    }

    public function applyrefCode(Request $request)
    {
        $ref_Code = $request->Refcode;
        if($request->user()->ref_by == null and $ref_Code != $request->user()->ref_code){
            try {
                $users = DB::select('select * from users where ref_code = ?', [$ref_Code]);
                if($users){
                   DB::update('update users set ref_by = ? where id = ?', [$ref_Code, $request->user()->id]);
                    $addingcoin = new AllFunction();
                    $add = $addingcoin->addCoins(30, $request->user()->id);
                    if($add){
                        $notify = $addingcoin->sendNotification('Refered coins has been added', $request->user()->id);
                        return response()->json(['status'=>true, 'msg'=>'Coin Added to your account'], 200);
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
            $buy = DB::insert('insert into buycoins (tranfer_id, user_id, coins, price, place) values (?, ?, ?, ?, ?)',
            [$request->transid, $request->user()->id, $request->coin, $request->price, $request->place]);
            if($buy == 1){
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
            $or = DB::insert('insert into orders (user_id, link, action, quantity, coins) values (?, ?, ?, ?, ?)', 
            [$request->user()->id, $request->link, $request->act, $request->quantity, $request->orcoins]);
            if($or == 1){
                $urcoin = DB::table('users')->where('id', $request->user()->id)->decrement('tot_coins', $request->orcoins);
                if($urcoin){
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
        $users = DB::select('select * from orders where done = ?', [0]);
        return $users;
    }

    public function OrderCompleted(Request $request)
    {
        $users = DB::select('select * from orders where id = ?', [$request->oid]);
        $affected = DB::update('update orders set done = 1 where id = ?', [$request->oid]);
        if($affected == 1){
                $notify = new AllFunction();
                $send = $notify->sendNotification('Your Order is Completed', $users[0]->user_id);
                return response()->json(['status'=>true, 'msg'=>'Order Completed'], 200);
        }
    }

    public function showNotifi(Request $request)
    {
        $users = DB::table('notification')->where('u_id', $request->user()->id)->orderByDesc('id')->paginate(10);
        return response()->json($users, 200);
    }    
    
    public function numberofNotif(Request $request)
    {
       $users = DB::select('select * from notification where seen = ? and u_id = ?', [0, $request->user()->id]);
       return response()->json(["Num"=>count($users)], 200);
    }
    
    public function getHistory(Request $request)
    {
        $users = DB::table('orders')->where('user_id', $request->user()->id)->orderByDesc('id')->paginate(10);
        return response()->json($users, 200);
    }

}
