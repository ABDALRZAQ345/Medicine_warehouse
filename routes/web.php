<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\MailVerificationController;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/verify-email/{id}/{hash}', [MailVerificationController::class, 'verify'])->name('verification.verify');

/// change password
Route::get('/', function () {
    return 'email sent successfully';
});
Route::get('/reset-password/{token}', [ChangePasswordController::class, 'Password_reset'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [ChangePasswordController::class, 'store'])->middleware('guest')->name('password.store');

//Route::post('/create-checkout-session',[\App\Http\Controllers\Stripe::class,'pay']);
//
//Route::get('/checkout', function (Request $request) {
//    $stripe = new \Stripe\StripeClient(config('stripe.api_key.secret'));
//    $customer = $stripe->customers->search(['email' => 'admin@admin.com']);
//    return response()->json(['customer' => $customer]);
//})->name('checkout');
//
//Route::view('/checkout/success', 'checkout.success')->name('checkout-success');
//Route::view('/checkout/cancel', 'checkout.cancel')->name('checkout-cancel');
////
//Route::get('/create-checkout-session',[\App\Http\Controllers\Stripe::class,'pay']);
//
//Route::post('/add', function (Request $request) {
//    $stripe = new \Stripe\StripeClient(config('stripe.api_key.secret'));
//    $product=$stripe->products->create([
//        'name' => 'prod1',
//        'description' => 'Gold Plan description',
//        'metadata' => [
//            'type' => 'cloth'
//        ]
//        ]);
//    $price = $stripe->prices->create([
//        'product' => $product->id, // Attach to the previously created product
//        'unit_amount' => 10 * 100, // Amount in cents, so this is $10.00
//        'currency' => 'usd',
//    ]);
//    $user=User::first();
//
//    return $user->checkout([$price->id => 20], [
//        'success_url' => route('checkout-success'),
//        'cancel_url' => route('checkout-cancel'),
//    ]);
//    ///
//
//    Route::get('/subscription-checkout', function (Request $request) {
//        return User::first()
//            ->newSubscription('default', 'price_basic_monthly')
//            ->trialDays(5)
//            ->allowPromotionCodes()
//            ->checkout([
//                'success_url' =>"http://127.0.0.1:8000/success",
//                'cancel_url' => "http://127.0.0.1:8000/cancel",
//            ]);
//    });
