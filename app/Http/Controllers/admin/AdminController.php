<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\User;

class AdminController extends Controller
{
    //
    public function __invoke()
    {
        $earned=Order::where('payment_status','=',1)->sum('total_price');
        $unpaid= Order::where('payment_status','=',0)->sum('total_price');
        $users=User::count();
        $medicines=Medicine::withoutTrashed()->count();
        $trashed_medicines=Medicine::onlyTrashed()->count();
        $delivered_orders=Order::where('status','=',2)->count();
        $undelivered_orders=Order::count()-$delivered_orders;
        $expired_medicines=Medicine::withTrashed()->where('expires_at','<',now())->count();
        $unexpired_medicines=Medicine::withoutTrashed()->count()-$expired_medicines;
        $month=now()->month;
       for($i=1;$i<=$month;$i++){
           $joined_users_last_months[$i]=User::whereYear('created_at', '=', now()->year)
               ->whereMonth('created_at','=',$i)
               ->whereRelation('roles','name','user')
               ->count();
       }

        $year=now()->year;
        $first_year = intval(date('Y', strtotime(User::min('created_at'))));

        for($i=$first_year;$i<=$year;$i++){
            $joined_users_last_years[$i]=User::whereYear('created_at', '=', $i)
                ->whereRelation('roles','name','user')
                ->count();
        }
        return response()->json([
            'earned'=>$earned,
            'unpaid'=>$unpaid,
            'users'=>$users,
            'medicines'=>$medicines,
            'trashed_medicines'=>$trashed_medicines,
            'delivered_orders'=>$delivered_orders,
            'undelivered_orders'=>$undelivered_orders,
            'expired_medicines'=>$expired_medicines,
            'unexpired_medicines'=>$unexpired_medicines,
            'joined_users_last_months'=>$joined_users_last_months,
            'joined_users_last_years'=>$joined_users_last_years,
        ]);

    }
}
