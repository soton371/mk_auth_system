<?php

namespace App\Http\Controllers;

use App\Models\ReferAccount;
use App\Models\ReferUser;
use App\Models\User;
use Illuminate\Http\Request;

class ReferAccountController extends Controller
{
    


    public function mining_balance()
    {
        $balance = ReferAccount::where("user_id", auth()->user()->id)->first();

        //$mining_balance = $balance->main_balance  * 0.01;
        $lowerBound = floor($balance->main_balance / 1000) * 1000;
        $percentage = 0.01;
        $mining_balance = $lowerBound * $percentage;

        $balance->update([
            "mining_balance"=> $balance->mining_balance + $mining_balance,
        ]);

        return response()->json([

            'data'      => $balance,
            'message'   => "Success",
            'status'    => 1,
        ]);

    }





    public function refer_user()
    {
        $refer_user = ReferUser::where('user_id', auth()->user()->id)->with('refer_user')->get();

        return response()->json([

            'data'      => $refer_user,
            'message'   => "Success",
            'status'    => 1,
        ]);

    }
    
    
    
    public function all_user()
    {
        $all_user = User::query()->with('balance')->withCount('refer_user')->get();

        return response()->json([

            'data'      => $all_user,
            'message'   => "Success",
            'status'    => 1,
        ]);

    }







}
