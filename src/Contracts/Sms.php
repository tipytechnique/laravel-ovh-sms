<?php

namespace TipyTechnique\LaravelOvhSms\Contracts;

use Ovh\Sms\Message;

interface Sms
{
    /**
     * Create the OVH SMS client
     *
     * @return self
     */
    public function createClient();

    /**
     * Helper to create a new Message instance easely
     *
     * @param string|array $to
     * @param bool         $isMarketing
     * @param bool         $allowResponse
     *
     * @return self
     */
    public function createMessage($to, bool $isMarketing, bool $allowResponse);

    /**
     * Send the message
     *
     * @param string $message
     *
     * @return array
     */
    public function send(string $message): array;

    /**
     * Get all messages from a given type ('outgoing', 'incoming', 'planned')
     *
     * @param string $type
     *
     * @return array
     */
    public function getMessages(string $type): array;

    /*
     * Get the blacklisted numbers
     *
     * @return array
     */
    public function getBlacklistedNumbers(): array;

    /**
     * Remove the given number from blacklist
     *
     * @param string $number
     */
    public function removeBlacklistedNumber(string $number): void;
}
