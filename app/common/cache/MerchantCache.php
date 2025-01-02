<?php

namespace app\common\cache;

use support\Redis;

class MerchantCache {
    // 登录失败次数
    private static $merchant_login_times = 'merchant:login_times:%s';

    // 商户管理员信息 管理员信息数组 [name:xx, mobile:xx, roles:[1, 2, 3] ...]
    private static $merchant_infos = 'merchant:info:%s';

    /**
     * 获取商户信息
     * @param string $id
     * @return array
     */
    public static function getMerchantInfo(string $id): array {
        $key = sprintf(self::$merchant_infos, $id);
        $data = Redis::get($key);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置商户信息
     * @param string $id
     * @param array $info
     * @return bool
     */
    public static function setMerchantInfo(string $id, array $info): bool {
        $key = sprintf(self::$merchant_infos, $id);
        return Redis::set($key, json_encode($info));
    }

    /**
     * 删除商户信息
     * @param string $id
     * @return bool
     */
    public static function delSystemInfo(string $id): bool {
        $key = sprintf(self::$merchant_infos, $id);
        return Redis::del($key);
    }

    /**
     * 获取登录失败次数
     * @param string $username
     * @return int
     */
    public static function getLoginTimes(string $username): int {
        $key = sprintf(self::$merchant_login_times, $username);
        return Redis::get($key) ?: 0;
    }

    /**
     * 增加登录失败次数，失效时间 5 分钟
     * @param string $username
     * @return int
     */
    public static function incrLoginTimes(string $username): int {
        $key = sprintf(self::$merchant_login_times, $username);
        Redis::incr($key);
        return Redis::expire($key, 300);
    }

    /**
     * 清除登录失败次数
     * @param string $username
     * @return bool
     */
    public static function clearLoginTimes(string $username): bool {
        $key = sprintf(self::$merchant_login_times, $username);
        return Redis::del($key);
    }

}