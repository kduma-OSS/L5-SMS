<?php
namespace KDuma\SMS;

use KDuma\SMS\Drivers\SMSDriverInterface;

class SMSSender
{
    /**
     * @var SMSDriverInterface
     */
    protected $driver;

    /**
     * SMSSender constructor.
     * @param SMSDriverInterface $driver
     */
    public function __construct(SMSDriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param $to      string   Recipient phone number
     * @param $message string   Message to send
     *
     * @return void
     */
    public function send($to, $message)
    {
        $this->driver->send($to, $message);
    }
}