<?php

namespace Sarkhanrasimoghlu\Lsim;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Sms', function () {
            return new Sms();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/sms.php',
            'sms'
        );

        $this->publishes([
            __DIR__ . '/../config/sms.php' => config_path('sms.php')
        ], 'sms-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/sms')
        ], 'sms-views');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sms');

    }

}
