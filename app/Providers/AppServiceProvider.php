<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\User;
use App\Observers\OrderObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Route::prefix('api/v2')
            ->middleware('api')
            ->group(base_path('routes/api_v2.php'));

        Model::shouldBeStrict(! app()->environment('production'));
        ///
        Model::preventLazyLoading(! app()->environment('production'));

        Cashier::useCustomerModel(User::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
        RateLimiter::for('email_verification', function (Request $request) {
            return Limit::perDay(20)->by($request->user()?->id ?: $request->ip());
        });
        Order::observe(OrderObserver::class);

    }
}
