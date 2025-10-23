<?php

namespace MuseWallet\SDK;

use MuseWallet\SDK\Services\MuseWalletService;
use Illuminate\Support\ServiceProvider;

/**
 * MuseWallet Service Provider
 *
 * Registers the MuseWallet SDK in the Laravel container
 */
class MuseWalletServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register MuseWalletService as singleton
        $this->app->singleton(MuseWalletService::class, function ($app) {
            return new MuseWalletService();
        });

        // Also register with a string binding for easier access
        $this->app->bind('musewallet.api', function ($app) {
            return $app->make(MuseWalletService::class);
        });

        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/musewallet.php',
            'musewallet'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish configuration file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/musewallet.php' => config_path('musewallet.php'),
            ], 'musewallet-config');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [MuseWalletService::class, 'musewallet.api'];
    }
}

