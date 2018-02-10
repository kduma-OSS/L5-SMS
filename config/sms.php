<?php

return [

    'default' => env('SMS_CHANNEL', 'log'),

    'channels' => [

        'log' => [
            'driver' => 'log',
            'level' => 'debug',
        ],

        // to use this driver you need to require kduma/sms-driver-serwersms
        'serwersms' => [
            'driver'   => 'serwersms',
            'login'    => env('SMS_SERWERSMS_LOGIN'),
            'password' => env('SMS_SERWERSMS_PASSWORD'),
            'sender'   => 'INFORMACJA',
            'eco'      => true,
            'flash'    => true,

            'test'     => true,
        ],

        // to use this driver you need to require kduma/sms-driver-justsend
        'justsend' => [
            'driver' => 'justsend',
            'key'    => env('SMS_JUSTSEND_KEY'),
            'sender' => 'INFORMACJA',
            'eco'    => true,
        ],

    ],

];
