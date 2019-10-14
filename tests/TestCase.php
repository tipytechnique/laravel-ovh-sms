<?php

namespace TipyTechnique\LaravelOvhSms\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Set up the environment.
     *
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('laravel-ovh-sms.app_key', 'dummy_key');
        $app['config']->set('laravel-ovh-sms.app_secret', 'dummy_secret');
        $app['config']->set('laravel-ovh-sms.consumer_key', 'dummy_consumer_key');
        $app['config']->set('laravel-ovh-sms.endpoint', 'dummy_endpoint');
        $app['config']->set('laravel-ovh-sms.sms_account', 'dummy_sms_account');
        $app['config']->set('laravel-ovh-sms.sms_user_login', 'dummy_sms_user_login');
        $app['config']->set('laravel-ovh-sms.sms_default_sender', 'dummy_sms_default_sender');
    }

    /**
     * Get my provider
     *
     * @param Application $app
     */
    protected function getPackageProviders($app)
    {
        return [
            \TipyTechnique\LaravelOvhSms\Tests\SmsServiceProvider::class,
        ];
    }

    /**
     * Get my Facade
     *
     * @param Appliation $app
     */
    protected function getPackageAliases($app)
    {
        return [
            'Sms' => \TipyTechnique\LaravelOvhSms\Facades\Sms::class,
        ];
    }
}
