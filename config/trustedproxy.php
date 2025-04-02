<?php

return [

    'proxies' => '*', // Trust all proxies

    'headers' => [
        'FORWARDED' => null,
        'X_FORWARDED_FOR' => 'X_FORWARDED_FOR',
        'X_FORWARDED_HOST' => 'X_FORWARDED_HOST',
        'X_FORWARDED_PORT' => 'X_FORWARDED_PORT',
        'X_FORWARDED_PROTO' => 'X_FORWARDED_PROTO',
    ],
];