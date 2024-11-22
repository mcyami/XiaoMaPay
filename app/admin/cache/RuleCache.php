<?php

namespace app\admin\cache;

use support\Redis;

/**
 * 系统权限缓存
 * [权限id => 权限KEY, 2 => app.admin.config.index, ...]
 */
class RuleCache {
    // 转义系统权限规则（app.admin.config.index）
    private static $system_rule = 'system:rule';
    // 原始系统权限规则（app\admin\config@index）
    private static $system_rule_raw = 'system:rule_raw';
    // 权限key=>title键值对
    private static $system_rule_title = 'system:rule_title';

    /**
     * 获取系统权限规则
     * @return array
     */
    public static function getSystemRule(): array {
        $data = Redis::get(self::$system_rule);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置系统权限规则
     * @param array $role
     * [权限id => 权限KEY, 2 => app.admin.config.index, ...]
     * @return bool
     */
    public static function setSystemRule(array $role): bool {
        return Redis::set(self::$system_rule, json_encode($role));
    }

    /**
     * 清除系统权限规则
     * @return bool
     */
    public static function delSystemRule(): bool {
        return Redis::del(self::$system_rule);
    }

    /**
     * 获取系统权限规则
     * @return array
     */
    public static function getSystemRuleRaw(): array {
        $data = Redis::get(self::$system_rule_raw);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置系统权限规则
     * @param array $role
     * [权限id => 权限KEY, 2 => app.admin.config.index, ...]
     * @return bool
     */
    public static function setSystemRuleRaw(array $role): bool {
        return Redis::set(self::$system_rule_raw, json_encode($role));
    }

    /**
     * 清除系统权限规则
     * @return bool
     */
    public static function delSystemRuleRaw(): bool {
        return Redis::del(self::$system_rule_raw);
    }

    /**
     * 获取权限key=>title缓存
     * @return array
     */
    public static function getSystemRuleTitle(): array {
        $data = Redis::get(self::$system_rule_title);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置系统权限key=>title缓存
     * @param array $role
     * [权限KEY => 权限名称, app\admin\controller\AdminController@insert=>新增, ...]
     * @return bool
     */
    public static function setSystemRuleTitle(array $role): bool {
        return Redis::set(self::$system_rule_title, json_encode($role));
    }

    /**
     * 清除系统权限key=>title缓存
     * @return bool
     */
    public static function delSystemRuleTitle(): bool {
        return Redis::del(self::$system_rule_title);
    }
}