<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\MailVerificationController;
use App\Http\Controllers\Medicine\MedicineController;
use App\Http\Controllers\Notification\NotificationsController;
use App\Http\Controllers\Orders\OrderController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;
use Stripe\StripeClient;


Route::middleware(['throttle:api', 'locale'])->group(function () {



    Route::middleware('guest')->group(function () {
        Route::post('/forget_password', [ChangePasswordController::class, 'ForgetPassword'])->name('forget_password');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
        //Route::get('/auth/google/callback',[SocialiteController::class,'callbackGoogle'])->name('auth.google.callback');

    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::group([], function () {

            Route::get('/notifications',[NotificationsController::class,'index'])->name('notifications.index');
            Route::put('/change_password', [ChangePasswordController::class, 'changePassword'])->name('change_password');
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::put('/update', [AuthController::class, 'update'])->name('update');
            Route::delete('/delete_account', [AuthController::class, 'delete'])->name('delete-account');
            Route::post('/resend_email_verification_link', [MailVerificationController::class, 'resend'])->middleware('throttle:email_verification')->name('resend-email-verification-link');

        });


        Route::group([], function () {
            Route::post('/medicines', [MedicineController::class, 'store'])->middleware('role:admin')->name('medicines.store');
            Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines.index');
            Route::get('/medicines/{medicine}', [MedicineController::class, 'show'])->name('medicines.show');
            Route::put('/medicines/{medicine}', [MedicineController::class, 'update'])->middleware('role:admin')->name('medicines.update');
            Route::delete('/medicines/{medicine}', [MedicineController::class, 'destroy'])->middleware('role:admin')->name('medicines.delete');
            Route::post('/medicines/{medicine}/restore', [MedicineController::class, 'restore'])->middleware('role:admin')->name('medicine.restore');
            Route::delete('/medicines/{medicine}/force_delete',[MedicineController::class, 'force_delete'])->middleware('role:admin')->name('medicine.force_delete');
            Route::post('/medicines/search', [MedicineController::class,'search'])->name('medicines.search.com');
            Route::post('/orders', [OrderController::class, 'store'])->middleware('role:user')->name('orders.store');
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::put('/orders/{order}', [OrderController::class, 'update'])->middleware('role:admin')->name('orders.update');
            Route::post('/get_order_invoice/{order}',[OrderController::class,'get_order_invoice'])->name('get_order_invoice');
        });

        Route::middleware(['role:admin'])->group(function () {
            Route::get('/admin_panel',[AdminController::class,'index'])->name('admin');
            Route::post('/change_role/{user}',[AdminController::class,'change_role'])->middleware('permission:chang_role_permission')->name('change_role');

        });



    });



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

