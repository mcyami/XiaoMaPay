<?php

namespace app\common\utils;

use app\common\cache\AdminCache;
use app\common\cache\RoleCache;
use app\common\cache\RuleCache;
use app\common\model\AdminModel;
use app\common\model\AdminRoleModel;
use app\common\model\RuleModel;
use support\exception\BusinessException;

class Auth {

    /**
     * 判断权限
     * 如果没有权限则抛出异常
     * @param string $controller
     * @param string $action
     * @return void
     * @throws \ReflectionException|BusinessException
     */
    public static function access(string $controller, string $action) {
        $code = 0;
        $msg = '';
        if (!static::canAccess($controller, $action, $code, $msg)) {
            throw new BusinessException($msg, $code);
        }
    }

    /**
     * 判断是否有权限
     * @param string $controller
     * @param string $action
     * @param int $code
     * @param string $msg
     * @return bool
     * @throws \ReflectionException|BusinessException
     */
    public static function canAccess(string $controller, string $action, int &$code = 0, string &$msg = ''): bool {
        // 无控制器信息说明是函数调用，函数不属于任何控制器，鉴权操作应该在函数内部完成。
        if (!$controller) {
            return true;
        }
        // 获取控制器鉴权信息
        $class = new \ReflectionClass($controller);
        $properties = $class->getDefaultProperties();
        $noNeedLogin = $properties['noNeedLogin'] ?? [];
        $noNeedAuth = $properties['noNeedAuth'] ?? [];

        // 不需要登录
        if (in_array($action, $noNeedLogin)) {
            return true;
        }

        // 获取登录信息
        $admin = AdminModel::admin();
        if (!$admin) {
            $msg = '请登录';
            // 401是未登录固定的返回码
            $code = 401;
            return false;
        }

        // 不需要鉴权
        if (in_array($action, $noNeedAuth)) {
            return true;
        }

        // 当前管理员无角色
        $roles = $admin['roles'];
        if (!$roles) {
            $msg = '无权限';
            $code = 2;
            return false;
        }

        // 角色没有规则
        $rule_ids = RuleModel::getRules($roles) ?? [];

        if (!$rule_ids) {
            $msg = '无权限';
            $code = 2;
            return false;
        }

        // 超级管理员
        if (in_array('*', $rule_ids)) {
            return true;
        }

        // 查询是否有当前控制器的规则 通过缓存的方式
        // 获取原始权限规则缓存
        $keys_cache = RuleCache::getSystemRuleRaw();
        $permissions = collect($keys_cache)->only($rule_ids)->values()->toArray();
        // 查询控制器规则是否在集合中
        $rule = collect($permissions)->first(function ($value) use ($controller, $action) {
            return $value === "$controller@$action" || $value === $controller;
        });

        if (!$rule) {
            $msg = '无权限';
            $code = 2;
            return false;
        }

        return true;
    }

    /**
     * 获取权限范围内的所有角色id
     * @param bool $with_self
     * @return array
     */
    public static function getScopeRoleIds(bool $with_self = false): array {
        if (!$admin = AdminModel::admin()) {
            return [];
        }
        $role_ids = $admin['roles'];
        $rules = RuleModel::getRules($role_ids);
        if ($rules && in_array('*', $rules)) {
            // 管理员，返回所有角色id
            return collect(RoleCache::getSystemRole())->keys()->toArray();
        }
        $roles = RoleCache::getSystemRoleRaw();
        $tree = new Tree($roles);
        $descendants = $tree->getDescendant($role_ids, $with_self);
        return array_column($descendants, 'id');
    }

    /**
     * 获取权限范围内的所有管理员id
     * @param bool $with_self
     * @return array
     */
    public static function getScopeAdminIds(bool $with_self = false): array {
        $role_ids = static::getScopeRoleIds();
        // 获取角色下的管理员缓存
        $role_has_admin = AdminCache::getSystemAdminRole();
        $admin_ids = collect($role_has_admin)->only($role_ids)->values()->collapse()->toArray();
        if ($with_self) {
            $admin_ids[] = AdminModel::adminId();
        }
        return array_unique($admin_ids);
    }

    /**
     * 是否是超级管理员
     * @param int $admin_id
     * @return bool
     */
    public static function isSuperAdmin(int $admin_id = 0): bool {
        if (!$admin_id) {
            if (!$roles = AdminModel::admin('roles')) {
                return false;
            }
        } else {
            $roles = AdminRoleModel::where('admin_id', $admin_id)->pluck('role_id');
        }
        $rules = RuleModel::getRules($roles);
        return $rules && in_array('*', $rules);
    }

}