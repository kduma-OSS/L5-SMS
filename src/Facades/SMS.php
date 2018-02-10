<?php
namespace KDuma\SMS\Facades;

use Illuminate\Support\Facades\Facade;
use KDuma\SMS\SMSManager;

class SMS extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return SMSManager::class;
    }
}