<?php

namespace app\admin\cache;

use support\Redis;

/**
 * 支付通道缓存
 */
class PayChannelCache {
    // 支付方式可用的驱动列表
    private static $pay_channel = 'system:pay_channel';

    /**
     * 获取支付通道列表
     * @return array
     */
    public static function getList(): array {
        $data = Redis::get(self::$pay_channel);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置支付通道列表
     * @param array $config
     * @return bool
     */
    public static function setList(array $list): bool {
        return Redis::set(self::$pay_channel, json_encode($list));
    }

    /**
     * 清除支付方式列表
     * @return bool
     */
    public static function delList(): bool {
        return Redis::del(self::$pay_channel);
    }
}