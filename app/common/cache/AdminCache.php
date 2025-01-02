<?php
namespace app\common\cache;

use support\Redis;

class AdminCache {
    // 登录失败次数
    private static $system_login_times = 'system:login_times:%s';

    // 管理员信息 管理员信息数组 [name:xx, mobile:xx, roles:[1, 2, 3] ...]
    private static $system_admin = 'system:admin:%s';

    // 管理员角色关联
    private static $system_admin_role = 'system:admin_role';

    /**
     * 获取管理员信息
     * @param string $id
     * @return array
     */
    public static function getSystemAdmin(string $id): array {
        $key = sprintf(self::$system_admin, $id);
        $data = Redis::get($key);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置管理员信息
     * @param string $id
     * @param array $info
     * @return bool
     */
    public static function setSystemAdmin(string $id, array $info): bool {
        $key = sprintf(self::$system_admin, $id);
        return Redis::set($key, json_encode($info));
    }

    /**
     * 删除管理员信息
     * @param string $id
     * @return bool
     */
    public static function delSystemAdmin(string $id): bool {
        $key = sprintf(self::$system_admin, $id);
        return Redis::del($key);
    }

    /**
     * 获取角色与管理员关联数据
     * @return array
     */
    public static function getSystemAdminRole(): array {
        $data = Redis::get(self::$system_admin_role);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * 设置系统角色及其管理员id
     * @param array $roles
     * [角色id => 管理员id数组 [1, 3, 5, 7, 8], ...]
     * @return bool
     */
    public static function setSystemAdminRole(array $roles): bool {
        return Redis::set(self::$system_admin_role, json_encode($roles));
    }

    /**
     * 清除系统角色及其权限
     * @return bool
     */
    public static function delSystemAdminRole(): bool {
        return Redis::del(self::$system_admin_role);
    }

    /**
     * 获取登录失败次数
     * @param string $username
     * @return int
     */
    public static function getLoginTimes(string $username): int {
        $key = sprintf(self::$system_login_times, $username);
        return Redis::get($key) ?: 0;
    }

    /**
     * 增加登录失败次数，失效时间 5 分钟
     * @param string $username
     * @return int
     */
    public static function incrLoginTimes(string $username): int {
        $key = sprintf(self::$system_login_times, $username);
        Redis::incr($key);
        return Redis::expire($key, 300);
    }

    /**
     * 清除登录失败次数
     * @param string $username
     * @return bool
     */
    public static function clearLoginTimes(string $username): bool {
        $key = sprintf(self::$system_login_times, $username);
        return Redis::del($key);
    }
}