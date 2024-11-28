<?php

namespace app\admin\cache;

use support\Redis;

/**
 * 支付驱动缓存
 */
class PayDriverCache {
    // 全部支付驱动列表缓存键名
    private static $pay_driver = 'system:pay_driver';


    /**
     * 获取支付驱动列表
     * @return array
     */
    public static function getList(): array {
        $data = Redis::get(self::$pay_driver);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置支付驱动列表
     * @param array $config
     * @return bool
     */
    public static function setList(array $list): bool {
        return Redis::set(self::$pay_driver, json_encode($list));
    }

    /**
     * 清除支付驱动列表
     * @return bool
     */
    public static function delList(): bool {
        return Redis::del(self::$pay_driver);
    }
}