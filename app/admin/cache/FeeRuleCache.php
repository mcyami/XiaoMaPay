<?php

namespace app\admin\cache;

use support\Redis;

/**
 * 手续费分账规则缓存
 */
class FeeRuleCache {
    // 分账规则缓存
    private static $fee_rule = 'system:fee_rule';

    /**
     * 获取分账规则列表
     * @return array
     */
    public static function getList(): array {
        $data = Redis::get(self::$fee_rule);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置分账规则列表
     * @param array $config
     * @return bool
     */
    public static function setList(array $list): bool {
        return Redis::set(self::$fee_rule, json_encode($list));
    }

    /**
     * 清除分账规则列表
     * @return bool
     */
    public static function delList(): bool {
        return Redis::del(self::$fee_rule);
    }
}