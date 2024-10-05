<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\UserManagementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\MailVerificationController;
use App\Http\Controllers\Favourite\FavouriteController;
use App\Http\Controllers\ManufacturerController\ManufacturerController;
use App\Http\Controllers\Medicine\MedicineController;
use App\Http\Controllers\Notification\NotificationsController;
use App\Http\Controllers\Orders\OrderController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:api', 'locale'])->group(function () {

    // Guest Routes (Unprotected Routes)
    Route::middleware('guest')->group(function () {
        Route::post('/forget_password', [ChangePasswordController::class, 'ForgetPassword'])->name('forget_password');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
        Route::get('/auth/google/callback', [SocialiteController::class, 'callbackGoogle'])->name('auth.google.callback');
    });

    // Authenticated User Routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::group([], function () {
            Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
            Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
            Route::put('/change_password', [ChangePasswordController::class, 'changePassword'])->name('change_password');
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::put('/account/update', [AuthController::class, 'update'])->name('update');
            Route::delete('/account/delete', [AuthController::class, 'delete'])->name('delete-account');
            Route::post('/email/resend', [MailVerificationController::class, 'resend'])->middleware('throttle:email_verification')->name('resend-email-verification-link');
        });

        Route::group([], function () {

            Route::post('/medicines', [MedicineController::class, 'store'])->middleware('role:admin')->name('medicines.store');
            Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines.index');
            Route::get('/medicines/{medicine}', [MedicineController::class, 'show'])->name('medicines.show');
            Route::put('/medicines/{medicine}', [MedicineController::class, 'update'])->middleware('role:admin')->name('medicines.update');
            Route::delete('/medicines/{medicine}', [MedicineController::class, 'destroy'])->middleware('role:admin')->name('medicines.delete');
            Route::post('/medicines/{medicine}/restore', [MedicineController::class, 'restore'])->middleware('role:admin')->name('medicine.restore');
            Route::post('/medicines/{medicine}/favourites', [FavouriteController::class, 'store'])->name('favourites.store');
            Route::get('/favourites', [FavouriteController::class, 'index'])->name('favourites.index');
            Route::post('/orders', [OrderController::class, 'store'])->middleware(['role:user', 'email_verified'])->name('orders.store');
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::put('/orders/{order}', [OrderController::class, 'update'])->middleware('role:admin')->name('orders.update');
            Route::post('/get_order_invoice/{order}', [OrderController::class, 'get_order_invoice'])->name('get_order_invoice');

        });

        Route::middleware(['role:admin'])->group(function () {
            Route::get('/admin_panel', [AdminController::class, 'panel'])->name('admin');
            Route::post('/users/{user}/change_role', [UserManagementController::class, 'change_role'])->middleware('permission:chang_role_permission')->name('change_role');
            Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('user.show');
            Route::get('/users', [UserManagementController::class, 'index'])->name('admins.index');
            Route::get('/manufacturer', [ManufacturerController::class, 'index'])->name('manufacturer.index');
            Route::post('/manufacturer', [ManufacturerController::class, 'store'])->name('manufacturer.store');
            Route::delete('/manufacturer/{manufacturer}', [ManufacturerController::class, 'destroy'])->name('manufacturer.destroy');
        });

    });

});
