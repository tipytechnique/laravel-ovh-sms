<?php

namespace TipyTechnique\LaravelOvhSms;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
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

        // resolve OvhSms as a singleton
        $this->app->singleton(Sms::class, function ($app) {
            return new OvhSms(config('laravel-ovh-sms'));
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
