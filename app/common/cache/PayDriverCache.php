<?php

namespace app\common\cache;

use support\Redis;

/**
 * 支付驱动缓存
 */
class PayDriverCache {
    // 全部支付驱动列表缓存键名
    private static $pay_driver = 'system:pay_driver';
    // 支付方式可用的驱动列表
    private static $pay_method_driver = 'system:pay_method_driver';
    // 转账方式可用的驱动列表
    private static $pay_trans_driver = 'system:pay_trans_driver';


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

    /**
     * 获取支付方式可用的驱动列表
     * @return array
     */
    public static function getMethodDriver(): array {
        $data = Redis::get(self::$pay_method_driver);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置支付方式可用的驱动列表
     * @param array $config
     * @return bool
     */
    public static function setMethodDriver(array $list): bool {
        return Redis::set(self::$pay_method_driver, json_encode($list));
    }

    /**
     * 清除支付方式可用的驱动列表
     * @return bool
     */
    public static function delMethodDriver(): bool {
        return Redis::del(self::$pay_method_driver);
    }

    /**
     * 获取付款方式可用的驱动列表
     * @return array
     */
    public static function getTransDriver(): array {
        $data = Redis::get(self::$pay_trans_driver);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置付款方式可用的驱动列表
     * @param array $config
     * @return bool
     */
    public static function setTransDriver(array $list): bool {
        return Redis::set(self::$pay_trans_driver, json_encode($list));
    }

    /**
     * 清除付款方式可用的驱动列表
     * @return bool
     */
    public static function delTransDriver(): bool {
        return Redis::del(self::$pay_trans_driver);
    }
}