[![Build Status](https://travis-ci.com/tipytechnique/laravel-ovh-sms.svg?branch=master)](https://travis-ci.com/tipytechnique/laravel-ovh-sms)
# laravel-ovh-sms

## Installation

You can install the package via composer:

``` bash
composer require tipytechnique/laravel-ovh-sms
```

## Usage

### Credentials
First you need to set your OVH credentials on your `.env` file :
```
OVHSMS_APP_KEY = "your-app-key"
OVHSMS_APP_SECRET = "your-app-secret"
OVHSMS_CONSUMER_KEY = "your-consumer-key"
OVHSMS_ACCOUNT = "your-sms-account"
OVHSMS_SENDER = "your-sms-sender"
```
To create your credentials, you can visit `https://api.ovh.com/createToken/index.cgi?GET=/sms&GET=/sms/*&PUT=/sms/*&DELETE=/sms/*&POST=/sms/*`

Optionally, you can publish this package's configuration file on your `config` folder by running 
```bash 
php artisan vendor:publish --provider="TipyTechnique\LaravelOvhSms\SmsServiceProvider"
```
If you are using the `Themosis` framework, run 
```bash 
php console vendor:publish --provider="TipyTechnique\LaravelOvhSms\SmsServiceProvider"
```

### Examples
#### Dependency injection vs Facade
You can either use the depency injection or the Facade to manage your sms.
``` php
// Using depency injection

use Illuminate\Routing\Controller as BaseController;
use TipyTechnique\LaravelOvhSms\Contracts\Sms;

class SmsController extends BaseController
{
    /**
     * Get all outgoing messages
     *
     * @param Sms $sms
     *
     * @return array
     */
    public function getAllSms(Sms $sms): array
    {
        return $sms->getMessages('incoming');
    }
}
```
``` php
// Using Facade

use Illuminate\Routing\Controller as BaseController;
use TipyTechnique\LaravelOvhSms\Facades\Sms;

class SmsController extends BaseController
{
    /**
     * Get all outgoing messages
     *
     * @param Sms $sms
     *
     * @return array
     */
    public function getAllSms(Sms $sms): array
    {
        return Sms::getMessages('incoming');
    }
}
```

#### Create a message
``` php
/**
 * First parameter  : receivers, can be a single one (string) or multiple (array)
 * Second parameter : isMarketing, true or false
 * Thir parameter   : allowResponse, true or false
 */
$message = Sms::createMessage('+33654213566', false, false);
$message = Sms::createMessage(['+33654213566', '+33652147895'], false, true);
```

#### Send a message
``` php
$message = Sms::createMessage('+33654213566', false, false);
$message->send('Hello world');

// Or with a single line
Sms::createMessage('+33654213566', false, false)->send('Hello world');
```

#### Get messages
``` php
// get ongoing messages
$messages = Sms::getMessages('ongoing');

// get incoming messages
$messages = Sms::getMessages('incoming');

// get planned messages
$messages = Sms::getMessages('planned');

// for ongoing and incoming messages, you can use a second argument to set filters
// all filters are optionals
$messages = Sms::getMessages(
    'ongoing', 
    [
        'dateStart' => '2019-07-01 12:00:00',
        'dateEnd' => '2019-08-01',
        'sender' => 'your-sender',
        'receiver' => 'a-receiver',
        'tag'  => 'a-tag'   
    ]
);
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Issue

Please submit your issue using the issue tracker.

## Credits

- [Yannick LEONE](https://github.com/tipytechnique)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.