<?php

namespace Shield\Shield\Providers;

use Illuminate\Support\ServiceProvider;
use Shield\Shield\Http\Middleware\Shield;

/**
 * Class ShieldServiceProvider
 *
 * @package \Shield\Shield\Providers
 */
class ShieldServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../resources/config/shield.php' => config_path('shield.php'),
        ], 'config');

        $this->app['router']->aliasMiddleware('shield', Shield::class);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../resources/config/shield.php',
            'shield'
        );
    }
}
