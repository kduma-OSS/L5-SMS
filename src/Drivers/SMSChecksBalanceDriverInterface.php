<?php
namespace KDuma\SMS\Drivers;

interface SMSChecksBalanceDriverInterface
{
    /**
     * @return int
     */
    public function balance();
}