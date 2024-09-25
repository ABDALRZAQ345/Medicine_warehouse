<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\MailVerificationController;
use App\Http\Controllers\Medicine\MedicineController;
use App\Http\Controllers\SocialiteController;
use App\Jobs\Fresh;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Support\Facades\Http;
use Stripe\StripeClient;

Route::middleware(['throttle:api', 'locale',
    // 'api_password'
])->group(function () {

    /// check app service provider
    ///

    Route::post('/subscribe', function (Request $request) {
        $request->validate([
            'payment_method' => 'required|string',
            'plan' => 'required|string', // E.g., "monthly", "yearly"
        ]);

        $stripe = new StripeClient(config('stripe.api_key.secret'));
        $user = Auth::user(); // Assuming the user is authenticated

        // Check if the user has a Stripe customer ID
        if (!$user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }

        // Create a subscription
        $subscription = $stripe->subscriptions->create([
            'customer' => $user->stripe_id,
            'items' => [['price' => $request->plan]],
            'default_payment_method' => $request->payment_method, // Use the provided payment method
        ]);

        return response()->json($subscription);
    });
    Route::get('/subscription', function () {
        $user = Auth::user(); // Assuming the user is authenticated

        if (!$user->hasStripeId()) {
            return response()->json(['error' => 'No subscription found'], 404);
        }

        $stripe = new StripeClient(config('stripe.api_key.secret'));
        $subscriptions = $stripe->subscriptions->all(['customer' => $user->stripe_id]);

        return response()->json($subscriptions);
    });

});

