<?php
return [
    'listen' => getenv('APP_LISTEN'),
    'transport' => 'tcp',
    'context' => [],
    'name' => getenv('APP_NAME') . '-' . getenv('APP_VERSION') . '-' . getenv('APP_ENV'),
    'count' => cpu_count() * 4,
    'user' => '',
    'group' => '',
    'reusePort' => false,
    'event_loop' => '',
    'stop_timeout' => 2,
    'pid_file' => runtime_path() . '/webmin.pid',
    'status_file' => runtime_path() . '/webmin.status',
    'stdout_file' => runtime_path() . '/logs/stdout.log',
    'log_file' => runtime_path() . '/logs/webmin.log',
    'max_package_size' => 10 * 1024 * 1024
];
