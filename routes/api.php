<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckRef;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');

Route::middleware('auth:api')->post('/coins', 'Func\UserData@addCoins');

Route::get('/getredeem', 'Func\UserData@generateRedeemCoin');
Route::middleware('auth:api')->post('/applycode', 'Func\UserData@applyredeem');

Route::middleware('auth:api')->post('/seen', 'Func\UserData@markseen');
Route::post('/check', 'Func\UserData@applyrefCode')->middleware(CheckRef::class, 'auth:api');

Route::middleware('auth:api')->post('/buy', 'Func\UserData@buyCoins');
Route::get('/allpayments', 'Func\payments@getAllPayments');

Route::delete('/donepay/{buyid}', 'Func\payments@markPaymentDone');

Route::post('/order', 'Func\UserData@order')->middleware('order', 'auth:api');
Route::get('/getorder', 'Func\UserData@getAllOrder');

Route::post('/markdone', 'Func\UserData@OrderCompleted');

Route::post('/addweb', 'Func\payments@addwebsite');
Route::middleware('auth:api')->get('/web', 'Func\payments@showWeb');
Route::middleware('auth:api')->get('/notification', 'Func\UserData@showNotifi');
Route::middleware('auth:api')->get('/numnoti', 'Func\UserData@numberofNotif');
Route::middleware('auth:api')->get('/history', 'Func\UserData@getHistory');


//hello akhil it is just a test