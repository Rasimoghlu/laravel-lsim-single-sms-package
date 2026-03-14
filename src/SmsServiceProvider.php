<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim;

use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;
use Sarkhanrasimoghlu\Lsim\Configuration\LsimConfiguration;
use Sarkhanrasimoghlu\Lsim\Contracts\ConfigurationInterface;
use Sarkhanrasimoghlu\Lsim\Contracts\HttpClientInterface;
use Sarkhanrasimoghlu\Lsim\Contracts\SmsServiceInterface;
use Sarkhanrasimoghlu\Lsim\Facades\SMS;
use Sarkhanrasimoghlu\Lsim\Http\GuzzleHttpClient;
use Sarkhanrasimoghlu\Lsim\Services\LsimSmsService;

class SmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/sms.php',
            'sms'
        );

        $this->registerConfiguration();
        $this->registerHttpClient();
        $this->registerSmsService();
        $this->registerFacade();
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/sms.php' => config_path('sms.php'),
        ], 'sms-config');
    }

    private function registerConfiguration(): void
    {
        $this->app->singleton(ConfigurationInterface::class, function (Application $app) {
            $config = $app->make('config')->get('sms', []);

            return LsimConfiguration::fromArray($config);
        });
    }

    private function registerHttpClient(): void
    {
        $this->app->singleton(HttpClientInterface::class, function (Application $app) {
            $config = $app->make(ConfigurationInterface::class);
            $verifySsl = $config->getVerifySsl();

            $guzzleClient = new Client([
                'timeout'     => $config->getTimeout(),
                'verify'      => $verifySsl,
                'http_errors' => false,
            ]);

            return new GuzzleHttpClient($guzzleClient, $config->getTimeout(), $verifySsl);
        });
    }

    private function registerSmsService(): void
    {
        $this->app->singleton(SmsServiceInterface::class, function (Application $app) {
            return new LsimSmsService(
                $app->make(HttpClientInterface::class),
                $app->make(ConfigurationInterface::class),
                $app->make(LoggerInterface::class),
            );
        });
    }

    private function registerFacade(): void
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('SMS', SMS::class);
    }

    public function provides(): array
    {
        return [
            ConfigurationInterface::class,
            HttpClientInterface::class,
            SmsServiceInterface::class,
        ];
    }
}
