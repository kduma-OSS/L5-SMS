<?php
namespace KDuma\SMS;

use Exception;
use Throwable;

class UnsupportedDriverFeatureException extends Exception
{
    public function __construct(string $feature, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Unsupported driver feature: ".$feature, $code, $previous);
    }
}