<?php
namespace KDuma\SMS\Drivers;

interface SMSSenderDriverInterface
{
    /**
     * @param $to      string   Recipient phone number
     * @param $message string   Message to send
     *
     * @return void
     */
    public function send($to, $message);
}