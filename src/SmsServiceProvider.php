<?php

namespace TipyTechnique\LaravelOvhSms;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Ovh\Sms\SmsApi;
use TipyTechnique\LaravelOvhSms\Contracts\Sms;

class SmsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register bindings in the container.
     */
    public function register()
    {
        // merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-ovh-sms.php',
            'laravel-ovh-sms'
        );

        $config = $this->app['config']->get('laravel-ovh-sms');

        // resolve SmsApi as a singleton
        $this->app->singleton(SmsApi::class, function ($app) use ($config) {
            $credentials = collect($config);
            $credentials = $credentials
                ->only(
                    [
                        'app_key',
                        'app_secret',
                        'consumer_key',
                        'endpoint'
                    ]
                )
                ->toArray();

            return new SmsApi(
                $credentials['app_key'],
                $credentials['app_secret'],
                $credentials['endpoint'],
                $credentials['consumer_key']
            );
        });

        // resolve OvhSms as a singleton
        $this->app->singleton(Sms::class, function ($app) use ($config) {
            return new OvhSms($app->make(SmsApi::class), $config);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->publishes(
            [
                __DIR__.'/../config/laravel-ovh-sms.php' => config_path('laravel-ovh-sms.php')
            ],
            'config'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [Sms::class];
    }
}
