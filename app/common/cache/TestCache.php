<?php
namespace app\common\cache;
use support\Redis;

/**
 * 缓存层示例
 */
class TestCache {
    private static $test_profile = 'test:%s:profile';

    public static function getProfile($test_id) {
        $key = sprintf(self::$test_profile, $test_id);
        $profile = Redis::connection()->get($key);
        if (empty($profile)) {
            return [];
        }
        return json_decode($profile, true);
    }

    public static function setProfile($test_id, $profile) {
        $key = sprintf(self::$test_profile, $test_id);
        return Redis::connection()->set($key, json_encode($profile));
    }
}