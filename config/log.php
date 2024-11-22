<?php
use support\Context;

return [
    'default' => [
        'handlers' => [
            [
                'class' => Monolog\Handler\RotatingFileHandler::class,
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    7, //$maxFiles
                    Monolog\Logger::DEBUG,
                ],
                'formatter' => [
                    'class' => Monolog\Formatter\LineFormatter::class,
                    'constructor' => ["[%datetime%] %channel%.%level_name%: trace_id:" . Context::get('re_trace_id') . " %message% %context% %extra%\n", 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
