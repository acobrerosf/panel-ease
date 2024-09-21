<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //////////////////////////////
        // Macros
        //////////////////////////////

        Carbon::macro('inApplicationTimezone', function () {
            /** @var Carbon $this */
            $carbon = $this;

            return $carbon->tz(config('app.timezone_display'));
        });
    }
}
