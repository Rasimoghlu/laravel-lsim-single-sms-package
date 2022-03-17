<?php

namespace Sarkhanrasimoghlu\Lsim;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Sarkhanrasimoghlu\Lsim\Facade\SmsFacade;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('sms', SmsFacade::class);

        $this->app->singleton('sms', function () {
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
    }

}
