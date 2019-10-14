<?php

namespace TipyTechnique\LaravelOvhSms\Tests;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Mockery;
use Ovh\Sms\SmsApi;
use TipyTechnique\LaravelOvhSms\Contracts\Sms;
use TipyTechnique\LaravelOvhSms\OvhSms;

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

        // resolve OvhSms as a singleton
        $this->app->singleton(Sms::class, function ($app) use ($config) {
            // mock Sms Api
            $smsApi = Mockery::mock(SmsApi::class);
            $smsApi->shouldReceive('getAccounts')
                ->andReturn(['dummy_sms_account']);
            $smsApi->shouldReceive('setAccount')
                ->andReturn(true);
            $smsApi->shouldReceive('setUser')
                ->andReturn(true);
            $smsApi->shouldReceive('getSenders')
                ->andReturn(['dummy_sms_default_sender']);

            return new OvhSms($smsApi, $config);
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
