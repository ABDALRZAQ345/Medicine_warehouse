<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Lcobucci\JWT\Exception;

/**
 * @subgroupDescription  forget_password
 * @group Authorization
 */
class ChangePasswordController extends Controller
{
    /**
     * @header  Authorization Bearer access_token
     * @authenticated
     */
    public function ForgetPassword(Request $request)
    {


        $request->validate([
            'email' => ['required', 'email:dns', 'exists:users,email'],
        ]);

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
        } catch (Exception $e) {
            return response()->json(['error' => 'some thing went wrong check your internet and try again ']);
        }


        return response()->json(__($status));

    }

    public function Password_reset(string $token)
    {

        return view('password.reset', ['token' => $token, 'email' => request('email')]);

    }

    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect('/')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);

    }
    ///

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @authenticated
     */
    public function changePassword(ChangePasswordRequest $request)
    {

        $request->validated();

        $user = Auth::user();

        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json([
                'message' => 'Your password has been changed'
            ]);
        } else {
            return response()->json([
                'message' => 'Your old password is incorrect'
            ], 401);
        }

    }
}
