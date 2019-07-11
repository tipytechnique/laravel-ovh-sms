<?php

namespace TipyTechnique\LaravelOvhSms;

use Exception;
use Ovh\Sms\Message;
use Ovh\Sms\SmsApi;
use TipyTechnique\LaravelOvhSms\Contracts\Sms;

/**
 * Class OvhSms
 *
 * @package TipyTechnique\LaravelOvhSms
 */
class OvhSms implements Sms
{
    /**
     * OVH SMS API Credentials
     *
     * @var array
     */
    private $credentials = [
        'app_key'      => null,
        'app_secret'   => null,
        'consumer_key' => null,
        'endpoint'     => null
    ];

    /**
     * Default account
     *
     * @var string|null
     */
    private $defaultAccount;

    /**
     * User login
     *
     * @var string|null
     */
    private $userLogin;

    /**
     * Default sender
     *
     * @var string|null
     */
    private $defaultSender;

    /**
     * SmsApi Client
     *
     * @var SmsApi|null
     */
    private $client;

    /**
     * Message instance
     *
     * @var Message
     */
    private $message;

    /**
     * Constructor
     *
     * @param array $credentials
     */
    public function __construct(array $credentials = [])
    {
        $credentials = collect($credentials);

        if (! $credentials->isEmpty()) {
            $this->credentials = $credentials
                ->only(
                    [
                        'app_key',
                        'app_secret',
                        'consumer_key',
                        'endpoint'
                    ]
                )
                ->toArray();
        } else {
            $this->loadCredentialsFromConfig();
        }

        // load properties
        $this->loadDefaultAccount()
             ->loadUserLogin()
             ->loadDefaultSender()
             ->createClient();
    }

    /**
     * Load API credentials from Laravel config
     *
     * @return self
     */
    protected function loadCredentialsFromConfig(): self
    {
        $this->credentials['app_key']      = config('laravel-ovh-sms.app_key');
        $this->credentials['app_secret']   = config('laravel-ovh-sms.app_secret');
        $this->credentials['consumer_key'] = config('laravel-ovh-sms.consumer_key');
        $this->credentials['endpoint']     = config('laravel-ovh-sms.endpoint');

        return $this;
    }

    /**
     * Load the default SMS account from config
     *
     * @return self
     */
    private function loadDefaultAccount(): self
    {
        $accountId = config('laravel-ovh-sms.sms_account', null);

        $this->defaultAccount = $accountId;

        return $this;
    }

    /**
     * Load the SMS user login from config
     *
     * @return self
     */
    private function loadUserLogin(): self
    {
        $userId = config('laravel-ovh-sms.sms_user_login', null);

        $this->userLogin = $userId;

        return $this;
    }

    /**
     * Load the default sender
     *
     * @return self
     */
    private function loadDefaultSender(): self
    {
        $defaultSender = config('laravel-ovh-sms.sms_default_sender', null);

        $this->defaultSender = $defaultSender;

        return $this;
    }

    /**
     * Create the SmsApi client.
     *
     * @return self
     */
    public function createClient(): self
    {
        $this->client = new SmsApi(
            $this->credentials['app_key'],
            $this->credentials['app_secret'],
            $this->credentials['endpoint'],
            $this->credentials['consumer_key']
        );

        // A default account is configured
        if (! is_null($this->defaultAccount)) {
            // Get all accounts from API
            $accounts = $this->client->getAccounts();

            // Given default account does not exist.
            if (! in_array($this->defaultAccount, $accounts)) {
                throw new Exception('Default SMS account '.$this->defaultAccount.' does not exist.');
            }

            $this->client->setAccount($this->defaultAccount);
        } else {
            throw new Exception('You must specify a default SMS account');
        }

        // A user is configured
        if (! is_null($this->user_login)) {
            $this->client->setUser($this->userLogin);
        }

        // A sender is configured
        if (! is_null($this->defaultSender)) {
            // Get available senders from API
            $senders = $this->client->getSenders();

            // If given sender does not exist.
            if (! in_array($this->defaultSender, $senders)) {
                throw new Exception('Default SMS sender '.$this->defaultSender.' does not exist.');
            }
        } else {
            throw new Exception('You must specify a default SMS sender.');
        }

        return $this;
    }

    /**
     * Get the OVH SMS API Client
     *
     * @return SmsApi
     */
    public function getClient(): SmsApi
    {
        return $this->client;
    }

    /**
     * Helper to create a new Message instance easely
     *
     * @param string|array $to
     * @param bool         $isMarketing
     * @param bool         $allowResponse
     *
     * @return self
     */
    public function createMessage($to, bool $isMarketing, bool $allowResponse): self
    {
        $message = $this->client->createMessage($allowResponse);
        $message->setIsMarketing($isMarketing);
        $message->setSender($this->defaultSender);

        // Convert receiver to an array of receivers.
        $to = ! is_array($to) ? [$to] : $to;

        foreach ($to as $receiver) {
            $message->addReceiver($receiver);
        }

        $this->message = $message;

        return $this;
    }

    /**
     * Send the message
     *
     * @param string $message
     *
     * @return array
     */
    public function send(string $message): array
    {
        return $this->message->send($message);
    }

    /**
     * Get all messages from a given type ('outgoing', 'incoming', 'planned')
     *
     * @param string $type
     * @param array  $args ['dateStart' => DateTime|null, 'dateEnd' => DateTim|nulle, 'sender' => string|null, 'receiver' => string|null, 'tag' => string|null]
     *
     * @return array
     */
    public function getMessages(string $type, array $args = []): array
    {
        $default = [
            'dateStart' => null,
            'dateEnd' => null,
            'sender' => null,
            'receiver' => null,
            'tag' => null
        ];

        $default = count($args) > 0 ? array_replace_recursive($default, $args) : $default;

        switch ($type) {
            case 'outgoing':
                $messages = $this->client->getOutgoingMessages(
                    $default['dateStart'],
                    $default['dateEnd'],
                    $default['sender'],
                    $default['receiver'],
                    $default['tag']
                );
                foreach ($messages as $message) {
                    $message->load();
                }

                return $messages;
                break;
            case 'incoming':
                $messages = $this->client->getIncomingMessages(
                    $default['dateStart'],
                    $default['dateEnd'],
                    $default['sender'],
                    $default['receiver'],
                    $default['tag']
                );
                foreach ($messages as $message) {
                    $message->load();
                }

                return $messages;
                break;
            case 'planned':
                $messages = $this->client->getPlannedMessages();
                foreach ($messages as $message) {
                    $message->load();
                }

                return $messages;
                break;
            default:
                throw new Exception('Provided type '.$type.' not exists.');
                break;
        }
    }
}
