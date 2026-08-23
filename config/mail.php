<?php

return [
    'default' => env('MAIL_MAILER', 'log'),
    'mailers' => [
        'log' => ['transport' => 'log', 'channel' => env('MAIL_LOG_CHANNEL')],
    ],
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@puskom.unima.ac.id'),
        'name' => env('MAIL_FROM_NAME', 'PUSKOM UNIMA'),
    ],
];
