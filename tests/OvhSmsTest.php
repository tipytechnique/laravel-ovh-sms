<?php

namespace TipyTechnique\LaravelOvhSms\Tests;

use Exception;
use Mockery;
use Ovh\Sms\Message;
use Ovh\Sms\Sms as OvhSms;
use TipyTechnique\LaravelOvhSms\Contracts\Sms;

class OvhSmsTest extends TestCase
{
    public function testGetCredentials(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $credentials = $ovhSms->getCredentials();

        $this->assertEquals(
            $credentials,
            [
                'app_key' => 'dummy_key',
                'app_secret' => 'dummy_secret',
                'consumer_key' => 'dummy_consumer_key',
                'endpoint' => 'dummy_endpoint'
            ]
        );
    }

    public function testDefaultAccount(): void
    {
        $ovhSms = $this->app->make(Sms::class);

        $this->assertSame($ovhSms->getDefaultSender(), 'dummy_sms_default_sender');
    }

    public function testUserLogin(): void
    {
        $ovhSms = $this->app->make(Sms::class);

        $this->assertSame($ovhSms->getUserLogin(), 'dummy_sms_user_login');
    }

    public function testDefaultSender(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $this->assertSame($ovhSms->getdefaultSender(), 'dummy_sms_default_sender');
    }

    public function testCreateMessageIsMarketing(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $message = Mockery::mock(Message::class);
        $mockClient = $ovhSms->getClient();
        $mockClient->shouldReceive('createMessage')
            ->once()
            ->with(true)
            ->andReturn($message);
        $message->shouldReceive('setIsMarketing')
            ->once()
            ->with(true);
        $message->shouldReceive('setSender')
            ->once()
            ->with($ovhSms->getDefaultSender());
        $message->shouldReceive('addReceiver');

        $ovhSms->createMessage('+33626541245', true, true);
    }

    public function testCreateMessageIsNotMarketing(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $message = Mockery::mock(Message::class);
        $mockClient = $ovhSms->getClient();
        $mockClient->shouldReceive('createMessage')
            ->once()
            ->with(true)
            ->andReturn($message);
        $message->shouldReceive('setIsMarketing')
            ->once()
            ->with(false);
        $message->shouldReceive('setSender')
            ->once()
            ->with($ovhSms->getDefaultSender());
        $message->shouldReceive('addReceiver');

        $ovhSms->createMessage('+33626541245', false, true);
    }

    public function testCreateMessageWithSingleReceiver(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $message = Mockery::mock(Message::class);
        $mockClient = $ovhSms->getClient();
        $mockClient->shouldReceive('createMessage')
            ->once()
            ->with(true)
            ->andReturn($message);
        $message->shouldReceive('setIsMarketing')
            ->once()
            ->with(false);
        $message->shouldReceive('setSender')
            ->once()
            ->with($ovhSms->getDefaultSender());
        $message->shouldReceive('addReceiver')
            ->once()
            ->with('+33626541245');

        $ovhSms->createMessage('+33626541245', false, true);
    }

    public function testCreateMessageWithMultipleReceivers(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $message = Mockery::mock(Message::class);
        $mockClient = $ovhSms->getClient();
        $mockClient->shouldReceive('createMessage')
            ->once()
            ->with(true)
            ->andReturn($message);
        $message->shouldReceive('setIsMarketing')
            ->once()
            ->with(false);
        $message->shouldReceive('setSender')
            ->once()
            ->with($ovhSms->getDefaultSender());
        $message->shouldReceive('addReceiver')
            ->times(3);
        $message->shouldReceive('addReceiver')
            ->with('+33626541245');
        $message->shouldReceive('addReceiver')
            ->with('+33626541246');
        $message->shouldReceive('addReceiver')
            ->with('+33626541247');
        $receivers = [
            '+33626541245',
            '+33626541246',
            '+33626541247'
        ];
        $ovhSms->createMessage($receivers, false, true);
    }

    public function testSendMessage(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $message = Mockery::mock(Message::class);
        $mockClient = $ovhSms->getClient();
        $mockClient->shouldReceive('createMessage')
            ->andReturn($message);
        $message->shouldReceive('setIsMarketing');
        $message->shouldReceive('setSender');
        $message->shouldReceive('addReceiver');
        $message->shouldReceive('send')
            ->once()
            ->with('Salut')
            ->andReturn([]);

        $ovhSms->createMessage('+33626541245', false, false)
            ->send('Salut');
    }

    public function testGetOutgoingMessages(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $sms = Mockery::mock(OvhSms::class);
        $sms->shouldReceive('load');
        $mockClient = $ovhSms->getClient();
        $mockClient->shouldReceive('getOutgoingMessages')
            ->andReturn([$sms]);

        $ovhSms->getMessages('outgoing');
    }

    public function testGetIncomingMessages(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $sms = Mockery::mock(OvhSms::class);
        $sms->shouldReceive('load');
        $mockClient = $ovhSms->getClient();
        $mockClient->shouldReceive('getIncomingMessages')
            ->andReturn([$sms]);

        $ovhSms->getMessages('incoming');
    }

    public function testGetPlannedMessages(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $sms = Mockery::mock(OvhSms::class);
        $sms->shouldReceive('load');
        $mockClient = $ovhSms->getClient();
        $mockClient->shouldReceive('getPlannedMessages')
            ->andReturn([$sms]);

        $ovhSms->getMessages('planned');
    }

    public function testGetMessagesNonExistingType(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Provided type dummy_type not exists.');

        $ovhSms->getMessages('dummy_type');
    }

    public function testSmsLoadMultipleTimes(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $sms = Mockery::mock(OvhSms::class);
        $sms->shouldReceive('load')
            ->times(3);
        $mockClient = $ovhSms->getClient();
        $mockClient->shouldReceive('getIncomingMessages')
            ->andReturn([$sms, $sms, $sms]);

        $ovhSms->getMessages('incoming');
    }

    public function testGetMessagesWithoutArgs(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $sms = Mockery::mock(OvhSms::class);
        $sms->shouldReceive('load');
        $mockClient = $ovhSms->getClient();
        $mockClient->shouldReceive('getIncomingMessages')
            ->with(null, null, null, null, null)
            ->andReturn([$sms]);

        $ovhSms->getMessages('incoming');
    }

    public function testGetMessagesWithArgs(): void
    {
        $ovhSms = $this->app->make(Sms::class);
        $sms = Mockery::mock(OvhSms::class);
        $sms->shouldReceive('load');
        $mockClient = $ovhSms->getClient();
        $mockClient->shouldReceive('getIncomingMessages')
            ->with(
                '2019-01-01',
                '2019-02-01',
                'sender',
                null,
                'tag'
            )
            ->andReturn([$sms]);

        $args = [
            'dateStart' => '2019-01-01',
            'dateEnd' => '2019-02-01',
            'sender' => 'sender',
            'receiver' => null,
            'tag' => 'tag'
        ];
        $ovhSms->getMessages('incoming', $args);
    }
}
