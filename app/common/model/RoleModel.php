<?php

namespace app\common\model;


use app\admin\cache\RoleCache;

/**
 * @property integer $id 主键(主键)
 * @property string $name 角色名
 * @property string $rules 权限
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property integer $pid 上级id
 */
class RoleModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'xm_roles';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    // 时间戳存储格式
    protected $dateFormat = 'U';

    /**
     * 获取权限id数组
     * @return array
     */
    public function getRuleIds() {
        return $this->rules ? explode(',', $this->rules) : [];
    }

    /**
     * 缓存所有角色及其权限
     * 新增、编辑、删除后需要进行缓存更新
     * @return bool
     */
    public static function cache(): bool {
        $roles = RoleModel::get();
        // id => rules权限数组 [1,2,3]
        $role = [];
        foreach ($roles as $item) {
            $role[$item->id] = $item->getRuleIds();
        }
        RoleCache::setSystemRole($role);
        RoleCache::setSystemRoleRaw($roles->toArray());
        return true;
    }

}
