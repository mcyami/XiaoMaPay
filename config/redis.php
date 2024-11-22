<?php

return [
    'default' => [
        'host'      => getenv('REDIS_HOST'),
        'password'  => getenv('REDIS_PASSWORD'),
        'port'      => getenv('REDIS_PORT'),
        'database'  => getenv('REDIS_DB'),
    ],
];
