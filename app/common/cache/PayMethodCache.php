<?php

namespace app\common\cache;

use support\Redis;

/**
 * 支付方式缓存
 */
class PayMethodCache {
    // 支付方式可用的驱动列表
    private static $pay_method = 'system:pay_method';

    /**
     * 获取支付方式列表
     * @return array
     */
    public static function getList(): array {
        $data = Redis::get(self::$pay_method);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置支付方式列表
     * @param array $config
     * @return bool
     */
    public static function setList(array $list): bool {
        return Redis::set(self::$pay_method, json_encode($list));
    }

    /**
     * 清除支付方式列表
     * @return bool
     */
    public static function delList(): bool {
        return Redis::del(self::$pay_method);
    }
}