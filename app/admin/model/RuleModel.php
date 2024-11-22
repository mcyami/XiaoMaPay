<?php

namespace app\admin\model;


use app\admin\cache\RoleCache;
use app\admin\cache\RuleCache;
use app\common\utils\Util;

/**
 * @property integer $id 主键(主键)
 * @property string $title 标题
 * @property string $icon 图标
 * @property string $key 标识
 * @property integer $pid 上级菜单
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $href url
 * @property integer $type 类型
 * @property integer $weight 排序
 */
class RuleModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'xm_rules';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    // 时间戳存储格式
    protected $dateFormat = 'U';

    const RULE_STATUS_ENABLE = 1; // 启用
    const RULE_STATUS_DISABLE = 0; // 禁用

    /**
     * 根据角色id数组获取权限规则
     * @param array $roles
     * @return array
     */
    public static function getRules(array $roles): array {
        // 获取角色缓存
        $role_cache = RoleCache::getSystemRole();
        if(!$role_cache) {
            // 缓存为空，重新生成缓存
            RoleModel::cache();
        }
        $role_collection = collect($role_cache);
        // 只返回 $roles 数组中存在的角色
        $roles_array = $roles ? $role_collection->only($roles) : [];
        $rules = [];
        foreach ($roles_array as $rule_ids) {
            $rules = array_merge($rules, $rule_ids);
        }
        return $rules;
    }

    /**
     * 缓存所有权限规则
     * 新增、编辑、删除后需要进行缓存更新
     * @return bool
     */
    public static function cache(): bool {
        $model = new RuleModel;
        $rules = $model->all();
        // 缓存仅启用的权限规则
        $enable_rules = $rules->where('status', RuleModel::RULE_STATUS_ENABLE);
        // 缓存1：id => 转义的key 74=>app.admin.admin.insert
        $rule = [];
        foreach ($enable_rules as $id => $item) {
            $key = $item['key'];
            if (!$key = Util::controllerToUrlPath($key)) {
                continue;
            }
            $code = str_replace('/', '.', trim($key, '/'));
            $rule[$id] = $code;
        }
        // 缓存2：id => 原始key 74=>app\admin\controller\AdminController@insert
        $rule_raw = $enable_rules->pluck('key', 'id')->toArray();
        // 缓存3： 原始key=>title app\admin\controller\AdminController@insert=>新增
        $rule_title = $rules->pluck('title', 'key')->toArray();

        // 缓存权限规则
        RuleCache::setSystemRule($rule);
        RuleCache::setSystemRuleRaw($rule_raw);
        RuleCache::setSystemRuleTitle($rule_title);
        return true;
    }

}
