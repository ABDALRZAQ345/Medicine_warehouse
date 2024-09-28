<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    //
    public function index(){

            $user = Auth::user();
            $notifications=$user->notifications()->select(['id','data','read_at','created_at'])->get();
            return response()->json([
            'notifications'=>$notifications
           ]);
    }
}
