<?php
namespace App\Classes;

use Illuminate\Support\Facades\DB;
use App\User;

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
        $data = DB::table('users')->where('id', $userid)->increment('tot_coins', $coins);
        if($data == 1){
            $users = DB::select('select * from users where id = ?', [$userid]);
            if($users[0]->tot_coins>100 && $users[0]->reffral==0){
                $affected = DB::table('users')->where('ref_code', $users[0]->ref_by)->increment('tot_coins', 50);
                $affected = DB::update('update users set reffral = 1 where id = ?', [$userid]);
                $results = DB::select('select * from users where ref_code = ?', [$users[0]->ref_by]);
                if($affected){
                    $this->sendNotification("50 coins of Reffering is added to your account", $results[0]->id);
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
      $getdta =  DB::insert('insert into notification (msg, u_id) values (?, ?)', [$msg, $uid]);
        return $getdta;
    }
}