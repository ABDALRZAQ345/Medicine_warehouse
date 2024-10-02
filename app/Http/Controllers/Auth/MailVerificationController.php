<?php

namespace App\Http\Controllers\Auth;

use App\Events\RegisteredEvent;
use App\Http\Controllers\Controller;
use App\Models\EmailVerificationToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @subgroupDescription email_verification
 *
 * @group Authorization
 */
class MailVerificationController extends Controller
{
    //
    public function verify(int $id, string $hash)
    {

        $user = User::findOrFail($id);

        $token = EmailVerificationToken::where('user_id', $id)->first();

        if (! $token || ! Hash::check($hash, $token->token) || $token->isExpired()) {
            return view('mail.email_verified_failed');
        }

        $token->delete();
        $user->email_verified_at = now();
        $user->save();

        return view('mail.email_verified_successfully');
    }

    /**
     * @header  Authorization Bearer access_token
     *
     * @authenticated
     */
    public function resend(Request $request)
    {
        $user = \Auth::user();

        EmailVerificationToken::where('user_id', $user->id)->delete();

        $str = str()->random(60);
        $user->Email_verification_tokens()->create([
            'token' => Hash::make($str),
            'expires_at' => now()->addHours(24),
        ]);

        event(new RegisteredEvent($user, $str));

        return response()->json([
            'message' => 'email verification link sent please check your email and verify your account',
        ]);
    }
}
