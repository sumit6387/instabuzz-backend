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
Route::group(['middleware' => ['auth:api']], function () {
	Route::get('/getorder', 'Func\UserData@getAllOrder');
    Route::get('/web', 'Func\payments@showWeb');
    Route::post('/coins', 'Func\UserData@addCoins');
    Route::post('/seen', 'Func\UserData@markseen');
    Route::post('/order', 'Func\UserData@order')->middleware('order');
    Route::post('/applycode', 'Func\UserData@applyredeem');
    Route::get('/notification', 'Func\UserData@showNotifi');
	Route::get('/numnoti', 'Func\UserData@numberofNotif');
	Route::get('/history', 'Func\UserData@getHistory');
	Route::post('/buy', 'Func\UserData@buyCoins');
});
Route::post('/markdone', 'Func\UserData@OrderCompleted');
Route::post('/addweb', 'Func\payments@addwebsite');
Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');
Route::post('/getredeem', 'Func\UserData@generateRedeemCoin');
Route::post('/check', 'Func\UserData@applyrefCode')->middleware(CheckRef::class, 'auth:api');
Route::get('/allpayments', 'Func\payments@getAllPayments');
Route::delete('/donepay/{buyid}', 'Func\payments@markPaymentDone');



// Route::middleware('auth:api')->post('/order','Func\UserData@order')->middleware('order');

// Route::middleware('auth:api')->post('/applycode', 'Func\UserData@applyredeem');

// Route::middleware('auth:api')->post('/seen', 'Func\UserData@markseen');


// Route::middleware('auth:api')->post('/buy', 'Func\UserData@buyCoins');


// Route::post('/order', 'Func\UserData@order')->middleware('order', 'auth:api');


// Route::middleware('auth:api')->get('/web', 'Func\payments@showWeb');
// Route::middleware('auth:api')->get('/notification', 'Func\UserData@showNotifi');
// Route::middleware('auth:api')->get('/numnoti', 'Func\UserData@numberofNotif');
// Route::middleware('auth:api')->get('/history', 'Func\UserData@getHistory');


//hello akhil it is just a test