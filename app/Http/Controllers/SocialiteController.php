<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Laravel\Socialite\Facades\Socialite;


class SocialiteController extends Controller
{
    /**
     * @return mixed
     * @group Authorization
     */
    public function redirectToGoogle()
    {
        return socialite::driver('google')->stateless()->redirect();
    }

    public function callbackGoogle()
    {
        try{
            $user = Socialite::driver('google')->stateless()->user();
            $finduser=User::where('email',$user->email)->first();
            if($finduser){
                return  response()->json([
                    'google_token' => $user->token,
                   'access_token' => $finduser->createToken('api token')->plainTextToken,
                   'user' => $user
                ]);
            }
            else{
             $new_user=User::create([
                'first_name'=>$user->name,
                'last_name' =>$user->name,
                'email'=>$user->email,
                'password'=> \Hash::make(str()->random(12)),
             ]);


                return response()->json([
                    'google_token' =>  $user->token,
                    'access_token' => $finduser->createToken('api token')->plainTextToken,
                    'user' => $user
                ]);

            }
        }
        catch (Exception $e)
        {
            dd($e);
        }
    }
}
