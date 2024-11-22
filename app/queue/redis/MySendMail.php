<?php

namespace app\queue\redis;

use support\Log;
use Webman\RedisQueue\Consumer;

class MySendMail implements Consumer {
    // 要消费的队列名
    public $queue = '_send_mail_';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';

    // 消费
    public function consume($data) {
        // 无需反序列化
//        var_export($data);
        Log::info('===consume消费成功===', [$data]);
    }

    public function onConsumeFailure(\Throwable $e, $package) {
        echo "consume failure\n";
        echo $e->getMessage() . "\n";
        // 无需反序列化
//        var_export($package);
        Log::info('===consume消费失败===', [$e->getMessage(), $package]);
    }
}
