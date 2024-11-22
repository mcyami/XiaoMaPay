<?php

namespace app\admin\cache;

use support\Redis;

/**
 * 系统配置缓存
 */
class ConfigCache {
    private static $system_config = 'system:config';


    /**
     * 获取系统配置
     * @return array
     */
    public static function getSystemConfig(): array {
        $data = Redis::get(self::$system_config);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置系统配置
     * @param array $config
     * @return bool
     */
    public static function setSystemConfig(array $config): bool {
        return Redis::set(self::$system_config, json_encode($config));
    }

    /**
     * 清除系统配置
     * @return bool
     */
    public static function delSystemConfig(): bool {
        return Redis::del(self::$system_config);
    }
}