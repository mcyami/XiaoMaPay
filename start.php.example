#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';

// +----------------------------------------------------------------------
// 解析命令行启动参数
// 启动命令：
// 开发：php start.php start -e APP_ENV=DEV
// 测试：php start.php start -e APP_ENV=TEST
// 生产：php start.php start -e APP_ENV=PROD
// +----------------------------------------------------------------------
foreach ($argv as $key => $value) {
    // 写入环境变量
    if ($value === '-e' && array_key_exists(($key + 1), $argv) && str_contains($argv[($key + 1)], '=')) {
        putenv($argv[($key + 1)]);
    }
}


support\App::run();
