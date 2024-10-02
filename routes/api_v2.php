<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Stripe\StripeClient;

Route::middleware(['throttle:api', 'locale',
    // 'api_password'
])->group(function () {

    /// check app service provider
    ///

    //    Route::post('/subscribe', function (Request $request) {
    //        $request->validate([
    //            'payment_method' => 'required|string',
    //            'plan' => 'required|string', // E.g., "monthly", "yearly"
    //        ]);
    //
    //        $stripe = new StripeClient(config('stripe.api_key.secret'));
    //        $user = Auth::user(); // Assuming the user is authenticated
    //
    //        // Check if the user has a Stripe customer ID
    //        if (!$user->hasStripeId()) {
    //            $user->createAsStripeCustomer();
    //        }
    //
    //        // Create a subscription
    //        $subscription = $stripe->subscriptions->create([
    //            'customer' => $user->stripe_id,
    //            'items' => [['price' => $request->plan]],
    //            'default_payment_method' => $request->payment_method, // Use the provided payment method
    //        ]);
    //
    //        return response()->json($subscription);
    //    });
    //    Route::get('/subscription', function () {
    //        $user = Auth::user(); // Assuming the user is authenticated
    //
    //        if (!$user->hasStripeId()) {
    //            return response()->json(['error' => 'No subscription found'], 404);
    //        }
    //
    //        $stripe = new StripeClient(config('stripe.api_key.secret'));
    //        $subscriptions = $stripe->subscriptions->all(['customer' => $user->stripe_id]);
    //
    //        return response()->json($subscriptions);
    //    });

    //Route::get('/pay',[\App\Http\Controllers\Stripe::class,'pay'])->name('pay');
    //
    //    Route::get('/subscription', function () {
    //        $user = Auth::user(); // Assuming the user is authenticated
    //
    //        if (!$user->hasStripeId()) {
    //            return response()->json(['error' => 'No subscription found'], 404);
    //        }
    //
    //        $stripe = new StripeClient(config('stripe.api_key.secret'));
    //        $subscriptions = $stripe->subscriptions->all(['customer' => $user->stripe_id]);
    //
    //        return response()->json($subscriptions);
    //    });
    //
    //    Route::post('/create-checkout-session',[\App\Http\Controllers\Stripe::class,'pay']);

    //    Route::get('/test', function () {
    //
    //        $user = Auth::user();
    //        $users= User::all()->except($user->id);
    //        Notification::send($users,new \App\Notifications\notification1($user));
    //        return $user->notifications;
    //    })->middleware(['auth:sanctum']);
    //
    //    Route::get('/notifications', function () {
    //       $user = Auth::user();
    //       $notifications =$user->notifications()->where('read_at',Null)->paginate(5);
    //       return $notifications;
    //    })->middleware(['auth:sanctum']);
    //    Route::get('/notification/{id}', function ($id) {
    //        $notification = Auth::user()->unreadNotifications()->findOrFail($id);
    //        $notification->markAsRead();
    //        return $notification;
    //    })->middleware('auth:sanctum');

});
