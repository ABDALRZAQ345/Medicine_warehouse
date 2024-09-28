<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    //
    public function index()
    {

        $data=Cache::remember('admin_panel',60*10,function (){
            $earned=Order::where('payment_status','=',1)->sum('total_price');
            $losses=Medicine::where('expires_at','<',now())->sum(DB::raw('quantity * price'));
            $unpaid= Order::where('payment_status','=',0)->sum('total_price');
            $paid_orders=Order::where('payment_status','=',1)->count();
            $unpaid_orders=Order::where('payment_status','=',0)->count();
            $users=User::count();
            $medicines=Medicine::withoutTrashed()->count();
            $trashed_medicines=Medicine::onlyTrashed()->count();
            $delivered_orders=Order::where('status','=',2)->count();
            $sent_orders=Order::where('status','=',1)->count();
            $repairing_orders=Order::where('status','=',0)->count();
            $expired_medicines=Medicine::withoutTrashed()->where('expires_at','<',now())->count();
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
            return [
                'earned'=>$earned,
                'paid_orders'=> $paid_orders, // number of unpaid orders
                'unpaid'=>$unpaid,  /// money must be paid
                'losses'=>$losses,  // total losses
                'unpaid_orders'=>$unpaid_orders,  // number of un paid orders
                'users'=>$users, // number of users in the application
                'medicines'=>$medicines, // number of medicines
                'trashed_medicines'=>$trashed_medicines, // number of trashed medicines
                'delivered_orders'=>$delivered_orders, // number of delivered orders
                'repairing_orders'=>$repairing_orders, // number of orders which are taking preaper
                'sent_orders'=>$sent_orders, // orders which have been sent
                'expired_medicines'=>$expired_medicines, // number of expired medicines
                'unexpired_medicines'=>$unexpired_medicines, // unexpired medicines
                'joined_users_last_months'=>$joined_users_last_months, // joined user in each month of the year
                'joined_users_last_years'=>$joined_users_last_years,// joined users in each year.
            ];
            });

        return response()->json([
            'message' => 'notice that this data will be updated each 10 minutes',
            'data'=>$data,
        ]);

    }
    public function change_role(Request $request,User $user){
        if($user===Auth::user()){
            return response()->json([
                'message'=>'you can change your role',
            ]);
        }
        $request->validate([
            'role'=>'required,in:admin,user'
        ]);

        $user->roles()->delete();
        $user->assignRole($request->role);
        return response()->json([
           'message' => 'role changed to '. $request->role ,
            'user_roles' => $user->roles,
        ]);
    }
}
