<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AdminServices;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    protected $adminService;

    protected $userService;

    public function __construct(AdminServices $adminServices, UserService $userService)
    {
        $this->adminService = $adminServices;
        $this->userService = $userService;
    }

    public function show($user)
    {
        $user = User::findOrFail($user);

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }

    public function index(Request $request)
    {
        $request->validate([
            'role' => ['nullable', 'exists:roles,name'],
            'email' => ['nullable', 'email:dns'],
        ]);

        $users = $this->userService->get_users($request);

        return UserResource::collection($users);
    }

    public function change_role(Request $request, User $user)
    {

        $request->validate([
            'role' => ['required', 'exists:roles,name'],
        ]);

        if ($user->id == Auth::id()) {
            return response()->json([
                'message' => 'you can not change your role',
            ]);
        }

        $this->userService->updateUserRole($user, $request->role);

        return response()->json([
            'message' => 'role changed to '.$request->role,
            'user_roles' => $user->roles,
        ]);

    }
}
