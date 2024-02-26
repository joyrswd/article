<?php

namespace App\Providers;

use App\Services\ArticleService;
use App\Services\AttributeService;
use App\Services\AuthorService;
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
    public const HOME = '/home';

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

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
        // {post}記事が存在するかチェック
        Route::bind('post', ArticleService::class);
        // {attr}属性が存在するかチェック
        Route::bind('attr', AttributeService::class);
        // {user}著者が存在するかチェック
        Route::bind('user', AuthorService::class);
        // {date}日付が正しいかチェック
        Route::bind('date', function($date){
            $d = \DateTime::createFromFormat('Y-m-d', $date);
            return ($d && $d->format('Y-m-d') === $date && $d < new \DateTime()) ? $date : abort(404);
        });

    }
}
