<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\EmailCode;
use App\Models\ReferAccount;
use App\Models\ReferUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;


class AuthController extends Controller
{


    
    private $service;


    /*
     |--------------------------------------------------------------------------
     | CONSTRUCTOR
     |--------------------------------------------------------------------------
    */
    public function __construct()
    {
    }




    /*
     |--------------------------------------------------------------------------
     | ACTIVE INACTIVE USER
     |--------------------------------------------------------------------------
    */
    public function active_inactive_user(Request $request)
    {

        $user = User::where('id',$request->id)->first();
        
        if ($user->is_active == 1) {

            $user->update([
                'is_active'      => 0,
            ]);

            return response()->json([
                'data'          =>NULL,
                'message'       => "Inactive Success",
                'status'        => 1,
            ]);

        }
        elseif( $user->is_active == 0) {

            $user->update([
                'is_active'      => 1,
            ]);
        }

        return response()->json([
            'data'          =>NULL,
            'message'       => "Active Success",
            'status'        => 1,
        ]);

    }




    /*
     |--------------------------------------------------------------------------
     | REGISTER
     |--------------------------------------------------------------------------
    */
    public function register(Request $request)
    {

        try {

            $refer_code = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ@#$&*'), 0, 14);

           // register an user
            $user = User::create([
                'name'          => $request->name,
                'phone'         => $request->phone,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'refer_code'    => $refer_code,
                'social_id'     => $request->social_id ?? '',
                'is_active'     => $request->is_active,
            ]);

            $refer_account = ReferAccount::updateOrCreate([
                'user_id'       => $user->id,
            ],[
                'main_balance'      => 500,
                'mining_balance'    => 0,
            ]);

            if ($request->by_refer_code) {

                $refer_user = User::where('refer_code', $request->by_refer_code)->first();
                $refer_account_user = ReferAccount::where('user_id', $refer_user->id)->first();

                ReferUser::create([
                    'user_id'=> $refer_user->id,
                    'referred_user_id'=> $user->id,
                ]);

                $refer_count = ReferUser::where('user_id', $refer_user->id)->count();

                $refer_count <=10 ? $refer_bonus = 100: $refer_bonus = 50;

                ReferAccount::updateOrCreate([
                    'user_id'=> $refer_user->id,
                ],[
                    'main_balance'=> $refer_account_user->main_balance + $refer_bonus,
                ]);

                // return response()->json([
                //     'count'         => $refer_count,
                //     'status'        => 1,
                //     'message'       => "Success"
                // ]);
            }

            // $mail_code = EmailCode::query()->where('email',$request->email)->first();
            // $mail_code->delete();

            return response()->json([

                'data'          => $user,
                'balance'       => $refer_account,
                'status'        => 1,
                'message'       => "Success"
            ]);
            
        } catch (Exception $e) {

            return response()->json([

                'data'          => $e->getMessage(),
                'status'        => 0,
                'message'       => "Error"
            ]);
        }

        
    }







    /*
     |--------------------------------------------------------------------------
     | LOGIN
     |--------------------------------------------------------------------------
    */
    public function login(Request $request)
    {
        
        // check username
        $user = User::where('email', $request->email)->first();

        // check user is exist or not
        if (!$user) {

            return response()->json([

                'data'      => "User Not Found",
                'message'   => "Error",
                'status'    => 0,
            ]);
        }

        // check password
        if(!Hash::check($request->password, $user->password)) {


            return response()->json([

                'data'      => "Password Not Match",
                'message'   => "Validation Error",
                'status'    => 0,
            ]);
        }

        // check user is exist or not
        if (!$user) {
            return response()->json([

                'data'      => "Mobile Number Not Found",
                'message'   => "Error",
                'status'    => 0,
            ]);
        }

        Auth::login($user);
        // create bearer token for authentication
        $data['token'] = $user->createToken('myapptoken')->plainTextToken;
        $data['user'] = $user;

        $balance = ReferAccount::where("user_id", auth()->user()->id)->first();


        return response()->json([

            'data'          => $data,
            'balance'       => $balance,
            'message'       => "Success",
            'status'        => 1,
        ]);
    }


    /*
     |--------------------------------------------------------------------------
     | SOCIAL LOGIN
     |--------------------------------------------------------------------------
    */
    public function social_login(Request $request)
    {
        
        // check username
        $user = User::where('social_id', $request->social_id)->first();

        $refer_code = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ@#$&*'), 0, 14);

        // check user is exist or not
        if (!$user) {

            $new_user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'refer_code'    => $refer_code,
                'social_id'     => $request->social_id,
            ]);

            $refer_account = ReferAccount::updateOrCreate([
                'user_id'       => $new_user->id,
            ],[
                'main_balance'      => 500,
                'mining_balance'    => 0,
            ]);

            if ($request->by_refer_code) {

                $refer_user = User::where('refer_code', $request->by_refer_code)->first();
                $refer_account_user = ReferAccount::where('user_id', $refer_user->id)->first();

                ReferUser::create([
                    'user_id'=> $refer_user->id,
                    'referred_user_id'=> $new_user->id,
                ]);

                $refer_count = ReferUser::where('user_id', $refer_user->id)->count();

                $refer_count <=10 ? $refer_bonus = 100: $refer_bonus = 50;

                ReferAccount::updateOrCreate([
                    'user_id'=> $refer_user->id,
                ],[
                    'main_balance'=> $refer_account_user->main_balance + $refer_bonus,
                ]);

                // return response()->json([
                //     'count'         => $refer_count,
                //     'status'        => 1,
                //     'message'       => "Success"
                // ]);
            }

            Auth::login($new_user);
            // create bearer token for authentication
            $data['token'] = $new_user->createToken('myapptoken')->plainTextToken;
            $data['user'] = $new_user;
    
    
            return response()->json([
    
                'data'          => $data,
                'balance'       => $refer_account,
                'message'       => "Success",
                'status'        => 1,
            ]);
        }
        else {
            Auth:: login($user);

            $data['token'] = $user->createToken('myapptoken')->plainTextToken;
            $data['user'] = $user;
    
            $balance = ReferAccount::where("user_id", auth()->user()->id)->first();
    
    
            return response()->json([
    
                'data'          => $data,
                'balance'       => $balance,
                'message'       => "Success",
                'status'        => 1,
            ]);

        }




    }


    /*
         |--------------------------------------------------------------------------
         | LOGOUT
         |--------------------------------------------------------------------------
        */
    public function logout(Request $request)
    {
    
        try {
        
            $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();

            return response()->json([
                'message' => 'Logged out successfully',
                'status' => 1,
            ]);
    
        } catch (Exception $ex) {
    
            return response()->json([
    
                'data'      => "User not login or Server Error",
                'status'    => 0,
                'message'   => "Error"
            ]);
        }
    }



    /*
     |--------------------------------------------------------------------------
     | TEST
     |--------------------------------------------------------------------------
    */
    public function test()
    {
        return response()->json([
            'message' => 'On the Auth',
            'status' => 1,
        ]);
    }


    /*
     |--------------------------------------------------------------------------
     | SEND MAIL
     |--------------------------------------------------------------------------
    */
    public function send_mail(Request $request)
    {

        try {
            $code = EmailCode::updateOrCreate(
                [
                    'email'         => $request->email
                ],
                [
                    'email'         => $request->email,
                    'code'          => mt_rand(1111,9999),
                ]);
    
                \Mail::to($request->email)->send(new \App\Mail\EmailCodeSend($code));
                return response()->json([
                        'data' => $code,
                        'message' => "Successfully mail send",
                        'status' => 1,
                    ]);
        } catch (\Exception $e) {
            // Log the exception and return an error response
            \Log::error($e->getMessage());
            return response()->json([
                'data'      => $e->getMessage(),
                'message' => "An error occurred while sending the email",
                'status' => 0,
            ]);
        }
    }






    /*
     |--------------------------------------------------------------------------
     | FORGOT PASSWORD
     |--------------------------------------------------------------------------
    */
    
    public function forgot_password(Request $request)
    {


        $user = User::query()->where('email',$request->email)->first();

        if (!$user) {

            return response()->json([

                'data'      => "User Not Found",
                'message'   => "Error",
                'status'    => 0,
            ]);
        }

        $user->update([
            'password'      => Hash::make($request->password),
        ]);

        $mail_code = EmailCode::query()->where('email',$request->email)->first();
        $mail_code->delete();

        return response()->json([

            'data'      => $user,
            'message'   => "Success",
            'status'    => 1,
        ]);
    }


    /*
     |--------------------------------------------------------------------------
     | EMAIL CHECK
     |--------------------------------------------------------------------------
    */
    public function check_email_exist(Request $request)
    {
        
        $user = User::query()->where('email',$request->email)->first();

        if (!$user) {

            return response()->json([
                'message'   => "Email wasn't used",
                'status'    => 1,
            ]);
        }
        else {

            return response()->json([
                'message'   => "Email already used",
                'status'    => 0,
            ]);
        }
    }




}
