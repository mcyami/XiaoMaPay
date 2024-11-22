<?php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [

    'type' => 'redis', // file or redis or redis_cluster

//    'handler' => FileSessionHandler::class,
    'handler' => RedisSessionHandler::class,
//    'handler' => RedisClusterSessionHandler::class,

    // 不同的handler使用不同的配置
    'config' => [
        // type为file时的配置
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // type为redis时的配置
        'redis' => [
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
            'auth'  => getenv('REDIS_PASSWORD'),
            'database' => getenv('REDIS_SID_DB'),
            'timeout' => 2,
            'prefix' => 'redis_session_',
        ],
        // type为redis_cluster时的配置
        'redis_cluster' => [
            'host' => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth' => '',
            'prefix' => 'redis_session_',
        ]
    ],

    'session_name' => 'PHPSID', // 存储session_id的cookie名

    'auto_update_timestamp' => false, // 是否自动刷新session，默认关闭

    'lifetime' => 7*24*60*60, // session过期时间

    'cookie_lifetime' => 365*24*60*60, // 存储session_id的cookie过期时间

    'cookie_path' => '/', // 存储session_id的cookie路径

    'domain' => '', // 存储session_id的cookie域名

    'http_only' => true, // 是否开启httpOnly，默认开启

    'secure' => false, // 仅在https下开启session，默认关闭

    'same_site' => '', // 用于防止CSRF攻击和用户追踪，可选值strict/lax/none

    'gc_probability' => [1, 1000], // 回收session的几率

];
