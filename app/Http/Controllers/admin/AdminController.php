<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


class AdminController extends Controller
{

    //
    public function index(AdminServices $adminServices)
    {

        $data = Cache::remember('admin_panel', 600, function () use ($adminServices) {
            return $adminServices->get_data();
        });

        return response()->json([
            'message' => 'notice that this data will be updated each 10 minutes',
            'data' => $data,
        ]);

    }

    public function change_role(Request $request, User $user)
    {

        if ($user->id == Auth::id()) {
            return response()->json([
                'message' => 'you can not change your role',
            ]);
        }
        $request->validate([
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user->roles()->detach();
        $user->assignRole($request->role);
        return response()->json([
            'message' => 'role changed to ' . $request->role,
            'user_roles' => $user->roles,
        ]);

    }
}
