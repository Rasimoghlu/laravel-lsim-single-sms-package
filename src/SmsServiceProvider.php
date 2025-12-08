<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim;

use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;
use Sarkhanrasimoghlu\Lsim\Configuration\LsimConfiguration;
use Sarkhanrasimoghlu\Lsim\Contracts\ConfigurationInterface;
use Sarkhanrasimoghlu\Lsim\Contracts\HttpClientInterface;
use Sarkhanrasimoghlu\Lsim\Contracts\SmsServiceInterface;
use Sarkhanrasimoghlu\Lsim\Http\GuzzleHttpClient;
use Sarkhanrasimoghlu\Lsim\Services\LsimSmsService;

/**
 * SMS Service Provider
 * 
 * Registers SMS services and configurations
 */
class SmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/sms.php',
            'sms'
        );

        $this->registerConfiguration();
        $this->registerHttpClient();
        $this->registerSmsService();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishConfiguration();
        $this->publishViews();
        $this->loadRoutes();
    }

    /**
     * Register configuration service
     */
    private function registerConfiguration(): void
    {
        $this->app->singleton(ConfigurationInterface::class, function (Application $app) {
            $config = $app->make('config')->get('sms.lsim', []);
            
            return LsimConfiguration::fromArray($config);
        });
    }

    /**
     * Register HTTP client service
     */
    private function registerHttpClient(): void
    {
        $this->app->singleton(HttpClientInterface::class, function (Application $app) {
            $config = $app->make(ConfigurationInterface::class);
            
            $guzzleClient = new Client([
                'timeout' => $config->getTimeout(),
                'verify' => true,
                'http_errors' => true,
            ]);

            return new GuzzleHttpClient($guzzleClient, $config->getTimeout());
        });
    }

    /**
     * Register SMS service
     */
    private function registerSmsService(): void
    {
        $this->app->singleton(SmsServiceInterface::class, function (Application $app) {
            return new LsimSmsService(
                $app->make(HttpClientInterface::class),
                $app->make(ConfigurationInterface::class),
                $app->make(LoggerInterface::class)
            );
        });
    }


    /**
     * Publish configuration files
     */
    private function publishConfiguration(): void
    {
        $this->publishes([
            __DIR__ . '/../config/sms.php' => config_path('sms.php')
        ], 'sms-config');
    }

    /**
     * Publish view files
     */
    private function publishViews(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/sms')
        ], 'sms-views');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sms');
    }

    /**
     * Load route files
     */
    private function loadRoutes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/sms.php');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            ConfigurationInterface::class,
            HttpClientInterface::class,
            SmsServiceInterface::class,
        ];
    }
}