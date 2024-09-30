<?php

namespace App\Http\Controllers\Auth;



use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequests\LoginRequest;
use App\Http\Requests\AuthRequests\SignupRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
/**
 *  @subgroupDescription  Log in, sign up, delete account ,update account information's  and log out
 * @group Authorization
*/
class AuthController extends Controller
{

    ///
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (!Auth::attempt($validated)) {
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


        $user = User::create($validated)->assignRole('user');

//        $str = str()->random(60);
//
//        $user->Email_verification_tokens()->create([
//            'token' => Hash::make($str),
//            'expires_at' => now()->addHours(24)
//        ]);
//
//        event(new RegisteredEvent($user,$str));

        $access_token= $user->createToken('access_token')->plainTextToken;

        return response()->json([
            'data' => new UserResource($user),
            'access_token' => $access_token ,
            'token_type' => 'Bearer',
            'role' => $user->roles->pluck('name')->first(),
            'message' => 'User registered successfully! Please check your email to verify your account.'
        ]);

    }
    /**
     * @header Authorization Bearer access_token
     * @authenticated
     */
    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully!']);
    }
    /**
     * @header Authorization Bearer access_token
     * @authenticated
     */
    public function delete()
    {
        $user = Auth::user();
        if($user->orders()->where('payment_status','=','0')->count() > 0){
            return response()->json([
                'message' => 'account cant be deleted because  you have  unpaid orders please complete payment first
                 \n if you think that you have paid all your orders please contact with us'
            ],400);
        }
        $user->tokens()->delete();
        User::find(Auth::id())->delete();
        return response()->json(['message' => 'account deleted successfully']);
    }

    /**
     * @header Authorization Bearer access_token
     * @authenticated
     */
    public function update(Request $request)
    {
        $validated = $request->validate([

        ]);

//        if ($request->photo) {
//            $filename = $request->photo->getClientOriginalName();
//            $filePath = 'photos/' . $filename;
//            if (!Storage::disk('public')->exists($filePath)) {
//                $filePath = $request->photo->store('photos', 'public');
//            }
//            $validated['photo'] = $filePath;
//        } else {
//            $validated['photo'] = 'photos/Untitled.png';
//        }


        $user = Auth::user();
        $user->update($validated);


        return response()->json([
            'data' => $user,
            'message' => 'updated successfully'
        ]);

    }



}
