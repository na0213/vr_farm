<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';
    public const ADMIN_HOME = '/admin/dashboard';//追加
    public const OWNER_HOME = '/owner/ownerdashboard';//追加
    public const SHOP_HOME = '/shop/dashboard';//追加

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::prefix('admin')->as('admin.')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/admin.php'));

            Route::prefix('owner')->as('owner.')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/owner.php'));

            Route::prefix('shop')->as('shop.')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/shop.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
