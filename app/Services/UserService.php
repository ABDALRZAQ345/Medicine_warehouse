<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function get_users($request)
    {
        $users = User::query();

        if ($request->filled('role')) {
            $users->whereRelation('roles', 'name', $request->role);
            //  $users->whereHas('roles', fn ($query) => $query->where('name', $request->role));
        }
        if ($request->filled('email')) {
            $users->where('email', $request->email);
        }
        $users->paginate();

        return $users;
    }

    public function updateUserRole(User $user, string $role)
    {
        DB::transaction(function () use ($user, $role) {
            $user = User::lockForUpdate()->find($user->id);
            $user->roles()->detach();
            $user->assignRole($role);
        });
    }

    public function createUser(array $data, string $role = 'user')
    {
        return DB::transaction(function () use ($data, $role) {

            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);
            $user->assignRole($role);

            $verificationToken = str()->random(60);
            $user->Email_verification_tokens()->create([
                'token' => Hash::make($verificationToken),
                'expires_at' => now()->addHours(24),
            ]);

            $user = User::find($user->id);

            return [$user, $verificationToken];
        });
    }
}
