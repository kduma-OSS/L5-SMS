<?php
namespace KDuma\SMS;

use KDuma\SMS\Drivers\SMSChecksBalanceDriverInterface;
use KDuma\SMS\Drivers\SMSSenderDriverInterface;
use KDuma\SMS\Exceptions\UnsupportedDriverFeatureException;

class SMSSender
{
    /**
     * @var SMSSenderDriverInterface | SMSChecksBalanceDriverInterface
     */
    protected $driver;

    /**
     * SMSSender constructor.
     * @param SMSSenderDriverInterface $driver
     */
    public function __construct(SMSSenderDriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param $to      string   Recipient phone number
     * @param $message string   Message to send
     *
     * @return mixed
     */
    public function send($to, $message)
    {
        return $this->driver->send($to, $message);
    }

    /**
     * @return int
     * @throws UnsupportedDriverFeatureException
     */
    public function balance()
    {
        if (!$this->driver instanceof SMSChecksBalanceDriverInterface)
            throw new UnsupportedDriverFeatureException('Balance Checking');

        return $this->driver->balance();
    }
}