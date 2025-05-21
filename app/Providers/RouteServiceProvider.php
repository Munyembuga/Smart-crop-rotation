<?php





















}    // ...existing code...    public const HOME = '/farmer/dashboard'; // Default to farmer dashboard     */
     * @var string     *     * Typically, users are redirected here after authentication.     *     * The path to your application's "home" route.    /**    // ...existing code...{class RouteServiceProvider extends ServiceProvideruse Illuminate\Support\Facades\Route;use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;

class RouteServiceProvider extends ServiceProvider
{
    // ...existing code...

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        // Register the admin middleware
        $this->app['router']->aliasMiddleware('admin', AdminMiddleware::class);

        // ...existing code...
    }

    // ...existing code...
}
