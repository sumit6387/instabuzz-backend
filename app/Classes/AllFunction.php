<?php
namespace App\Classes;

use App\User;
use App\notification;

class AllFunction{
    function random_strings($length_of_string) 
    { 
  
    // String of all alphanumeric character 
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
  
    // Shufle the $str_result and returns substring 
    // of specified length 
    return substr(str_shuffle($str_result),  
                       0, $length_of_string); 
    } 

    public function addCoins($coins, $userid)
    {
        $user = new User();
        $data = User::where('id', $userid)->increment('tot_coins', $coins);
        if($data == 1){
            $users = User::where('id', $userid)->get()->first();
            if($users->tot_coins>100 && $users->reffral==0){
                $affected = User::where('ref_code', $users->ref_by)->increment('tot_coins', 50);
                $affected = User::where('id',$userid)->get()->first();
                $affected->reffral = 1;
                $results = User::where('ref_code',$users->ref_by)->get()->first();
                if($affected->update()){
                    $this->sendNotification("50 coins of Reffering is added to your account", $results->id);
                    return true;
                }
            }else{
                return true;
            }
        }else{
            return false;
        }
    }

    public function sendNotification($msg, $uid)
    {
      $getdta = new notification();
      $getdta->msg =  $msg;
      $getdta->u_id =  $uid;
      $getdta->save();
      return 1;
    }
}