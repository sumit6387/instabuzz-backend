<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Classes\AllFunction;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserController extends Controller
{
    public function register(Request $request)
    { 
        try {
            $allfuntion = new AllFunction();
            $getrndnum = $allfuntion->random_strings(5);
            $user = new User();
            $token = Str::random(20);
            $checking = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required',
                'mobile' => 'required',
            ]);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile_num = $request->mobile;
            $user->api_token = $token;
            $user->password = Hash::make($request->pass);
            $user->ref_code = $getrndnum;
            $con = $user->save();
            if($con == 1){
                $data = ["status"=>true, "msg"=>"User Registered", "token"=>$token];
                return response()->json($data, 200);
            }  
        } catch (QueryException $e) {
            $data = ["status"=>false, "msg"=>"Please Check your Email and Number"];
            $errorCode = $e->errorInfo[1];
                if($errorCode == 1062){
                    return response()->json($data, 200);
                }
            }
        }

    public function login(Request $request)
    {
        try
        {
            $user = new User();
            $data = $user->where('mobile_num', $request->mobile)->first();
            if (Hash::check($request->pass, $data->password)) {
                return response()->json(["status"=>true,"token"=>$data->api_token], 200);
            }else{
                return response()->json(["status"=>false,"err"=>"Invalid password"], 200);
            }
        }
        catch(Exception $e)
        {
            return response()->json(["status"=>false, "err"=>"Mobile Number Not Found"], 200);;
        }
    }

}
