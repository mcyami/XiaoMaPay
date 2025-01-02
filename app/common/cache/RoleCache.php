<?php

namespace app\common\cache;

use support\Redis;

/**
 * 系统角色缓存
 * [角色id => 权限id数组 [1, 3, 5, 7, 8], 角色id => 权限id数组 [1, 3, 5, 7, 8], ...]
 */
class RoleCache {
    private static $system_role = 'system:role';

    // 原始系统角色数据
    private static $system_role_raw = 'system:role_raw';

    /**
     * 获取系统角色及其权限
     * @return array
     */
    public static function getSystemRole(): array {
        $data = Redis::get(self::$system_role);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置系统角色及其权限
     * @param array $role
     * [角色id => 权限id数组 [1, 3, 5, 7, 8], 角色id => 权限id数组 [1, 3, 5, 7, 8], ...]
     * @return bool
     */
    public static function setSystemRole(array $role): bool {
        return Redis::set(self::$system_role, json_encode($role));
    }

    /**
     * 清除系统角色及其权限
     * @return bool
     */
    public static function delSystemRole(): bool {
        return Redis::del(self::$system_role);
    }

    /**
     * 获取系统角色及其权限
     * @return array
     */
    public static function getSystemRoleRaw(): array {
        $data = Redis::get(self::$system_role_raw);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置系统角色及其权限
     * @param array $role
     * [角色id => 权限id数组 [1, 3, 5, 7, 8], 角色id => 权限id数组 [1, 3, 5, 7, 8], ...]
     * @return bool
     */
    public static function setSystemRoleRaw(array $role): bool {
        return Redis::set(self::$system_role_raw, json_encode($role));
    }

    /**
     * 清除系统角色及其权限
     * @return bool
     */
    public static function delSystemRoleRaw(): bool {
        return Redis::del(self::$system_role_raw);
    }
}