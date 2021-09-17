<?php

return [
    'oracle' => [
        'driver'         => 'oracle',
        'host'           => env('DB_HOST_ORA', ''),
        'port'           => env('DB_PORT_ORA', '1521'),
        'database'       => env('DB_DATABASE_ORA', ''),
        'service_name'   => env('DB_HOST_ORA', ''),
        'username'       => env('DB_USERNAME_ORA', ''),
        'password'       => env('DB_PASSWORD_ORA', ''),
        'charset'        => env('DB_CHARSET_ORA', 'AL32UTF8'),
        'prefix'         => env('DB_PREFIX_ORA', ''),
    ],
];
