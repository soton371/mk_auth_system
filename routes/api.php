<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReferAccountController;

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


Route::post('register',                 [AuthController::class, 'register']);
Route::post('login',                    [AuthController::class, 'login']);
Route::post('social_login',             [AuthController::class, 'social_login']);
Route::post('send_mail',                [AuthController::class, 'send_mail']);
Route::post('forgot_password',          [AuthController::class, 'forgot_password']);
Route::post('check_email_exist',        [AuthController::class, 'check_email_exist']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => ['auth:sanctum']], function() {

    // logout from device
        Route::post('logout',                       [AuthController::class,'logout']);
        Route::post('get_mining_balance',           [ReferAccountController::class,'mining_balance']);
        Route::post('active_inactive_user',         [AuthController::class,'active_inactive_user']);
        Route::get('refer_user',                    [ReferAccountController::class,'refer_user']);
        Route::get('all_user',                      [ReferAccountController::class,'all_user']);
        Route::get('test',                          [AuthController::class,'test']);
    });
