<?php

namespace App\Http\Controllers\Auth;

use App\Events\RegisteredEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequests\LoginRequest;
use App\Http\Requests\AuthRequests\SignupRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 *  @subgroupDescription  Log in, sign up, delete account ,update account information's  and log out
 *
 * @group Authorization
 */
class AuthController extends Controller
{
    ///
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (! Auth::attempt($validated)) {
            return response()->json(['error' => 'email or password are not correct '], 401);
        }

        $user = User::where('email', $validated['email'])->first();

        return response()->json([
            'data' => new userResource($user),
            'access_token' => $user->createToken('access_token')->plainTextToken,
            'token_type' => 'Bearer',
            'role' => $user->roles->pluck('name')->first(),
        ]);

    }

    public function register(SignupRequest $request)
    {
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        //        $photo_path = 'photos/Untitled.png';
        //        if ($request->photo)
        //            $photo_path = $request->photo->store('photos', 'public');
        //        $validated['photo'] = $photo_path;

        try {
            DB::beginTransaction();
            $user = User::create($validated)->assignRole('user');

            $str = str()->random(60);

            $user->Email_verification_tokens()->create([
                'token' => Hash::make($str),
                'expires_at' => now()->addHours(24),
            ]);
            $access_token = $user->createToken('access_token')->plainTextToken;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }

        event(new RegisteredEvent($user, $str));

        return response()->json([
            'data' => new UserResource(User::find($user->id)),
            'access_token' => $access_token,
            'token_type' => 'Bearer',
            'role' => $user->roles->pluck('name')->first(),
            'message' => 'User registered successfully! Please check your email to verify your account.',
        ]);

    }

    /**
     * @header Authorization Bearer access_token
     *
     * @authenticated
     */
    public function logout(Request $request)
    {

        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully!']);
    }

    /**
     * @header Authorization Bearer access_token
     *
     * @authenticated
     */
    public function delete()
    {
        $user = Auth::user();
        if ($user->orders()->where('payment_status', '=', '0')->count() > 0) {
            return response()->json([
                'message' => 'account cant be deleted because  you have  unpaid orders please complete payment first
                 \n if you think that you have paid all your orders please contact with us',
            ], 400);
        }
        if ($user->hasRole('admin')) {
            return response()->json([
                'message' => 'you cant change your account because you are admin in that application ',
            ]);
        }

        User::find($user->id)->delete();

        return response()->json(['message' => 'account deleted successfully']);
    }

    /**
     * @header Authorization Bearer access_token
     *
     * @authenticated
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['nullable', 'numeric', 'digits_between:10,12'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:10240'],
        ]);
        $user = Auth::user();

        try {
            DB::beginTransaction();
            if ($request->photo) {
                $validated['photo'] = $request->photo->store('profile/photos', 'public');
            }

            $user->update($validated);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'updated successfully',
        ]);

    }

    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }
}
