<?php

namespace app\common\service;

use support\Redis;

/**
 * 安全锁服务
 */
class SafetyLockService {
    /**
     * 加锁
     * @param string $lock_key 锁名称
     * @param int $lockTime 最长锁时间，过期
     * @return bool 加成功 true
     */
    public static function addLock($lock_key, $lockTime) {
        $check = Redis::setnx($lock_key, time());
        // 如果没设置成功，看ttl是否存在，如果不存在需要重新设置ttl
        if (!$check) {
            $ttl = Redis::ttl($lock_key);
            if ($ttl == -1) {
                Redis::expire($lock_key, $lockTime);
            }
            return false;
        }
        Redis::expire($lock_key, $lockTime);
        return true;
    }

    /**
     * 批量移除锁
     * @param $lock_keys
     * @return void
     */
    public static function removeLock($lock_keys) {
        if (is_array($lock_keys)) {
            foreach ($lock_keys as $item) {
                Redis::del($item);
            }
        } else {
            Redis::del($lock_keys);
        }
    }

    /**
     * 次数限制
     * @param $lock_key
     * @param $ttl
     * @return int
     */
    public static function countLock($lock_key, $ttl) {
        $count = Redis::incr($lock_key);
        if ($count == 1) {
            Redis::expire($lock_key, $ttl);
        }

        return $count;
    }

    /**
     * 次数限制
     * @param $lock_key
     * @return bool
     */
    public static function isLock($lock_key) {
        $rtn = Redis::get($lock_key);
        return $rtn;
    }

    /**
     * 锁剩余的有效时间
     * @param string $key
     * @return int
     */
    public static function lockTTL(string $key) {
        return Redis::ttl($key);
    }
}
