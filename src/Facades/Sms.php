<?php

namespace TipyTechnique\LaravelOvhSms\Facades;

use Illuminate\Support\Facades\Facade;
use TipyTechnique\LaravelOvhSms\Contracts\Sms as ContractSms;

class Sms extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ContractSms::class;
    }
}
