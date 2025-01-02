<?php

namespace app\common\model;

use app\common\cache\AdminCache;

/**
 * @property integer $id ID(主键)
 * @property string $username 用户名
 * @property string $nickname 昵称
 * @property string $password 密码
 * @property string $avatar 头像
 * @property string $email 邮箱
 * @property string $mobile 手机
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $login_at 登录时间
 * @property string $roles 角色
 * @property integer $status 状态 0正常 1禁用
 */
class AdminModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'xm_admins';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    // 时间戳存储格式
    protected $dateFormat = 'U';

    const ADMIN_STATUS_ENABLE = 1; // 启用
    const ADMIN_STATUS_DISABLE = 0; // 禁用

    /**
     * 通过用户名获取管理员信息
     * @param string $username
     * @return AdminModel|null
     */
    public static function getByUsername(string $username = '') {
        return self::where('username', $username)->first();
    }

    /**
     * 更新登录时间
     * @param AdminModel $admin
     * @return bool
     */
    public static function updateLoginTime(AdminModel $admin): bool {
        $admin->login_at = time();
        $admin->save();
        self::cache($admin->id);
        return true;
    }

    /**
     * 更新管理员资料
     * @param $admin_id
     * @param $data
     * @return bool|int
     */
    public static function updateProfile($admin_id, $data) {
        $res = self::where('id', $admin_id)->update($data);
        self::cache($admin_id);
        return $res;
    }

    /**
     * 当前管理员id
     * @return int|null
     */
    public static function adminId(): ?int {
        return session('admin.id');
    }

    /**
     * 当前管理员
     * @param null|array|string $fields
     *  string: 返回指定字段
     *  array: 返回指定字段
     *  null: 返回所有字段
     * @return array|mixed|null
     */
    public static function admin($fields = null) {
        self::refreshAdminSession();
        if (!$admin = session('admin')) {
            return null;
        }
        if ($fields === null) {
            return $admin;
        }
        if (is_array($fields)) {
            $results = [];
            foreach ($fields as $field) {
                $results[$field] = $admin[$field] ?? null;
            }
            return $results;
        }
        return $admin[$fields] ?? null;
    }

    /**
     * 刷新当前管理员session
     * @param bool $force 是否强制刷新
     * @return void|null
     */
    public static function refreshAdminSession(bool $force = false) {
        $admin_session = session('admin');
        if (!$admin_session) {
            return null;
        }
        $admin_id = $admin_session['id'];
        $time_now = time();
        // session在2秒内不刷新
        $session_ttl = 2;
        $session_last_update_time = session('admin.session_last_update_time', 0);
        if (!$force && $time_now - $session_last_update_time < $session_ttl) {
            return null;
        }
        $session = request()->session();
        // 从缓存获取管理员信息
        $admin = AdminCache::getSystemAdmin($admin_id);
        if (!$admin) {
            $session->forget('admin');
            return null;
        }
        $admin['password'] = md5($admin['password']);
        $admin_session['password'] = $admin_session['password'] ?? '';
        if ($admin['password'] != $admin_session['password']) {
            $session->forget('admin');
            return null;
        }
        // 账户被禁用
        if ($admin['status'] != self::ADMIN_STATUS_ENABLE) {
            $session->forget('admin');
            return;
        }
        $admin['session_last_update_time'] = $time_now;
        $session->set('admin', $admin);
    }

    /**
     * 缓存当前操作的管理员信息
     * 新增、编辑、删除后需要进行缓存更新
     * @param string $id
     * @param bool $del
     * @return bool
     */
    public static function cache(string $id, bool $del = false): bool {
        if ($del) {
            AdminCache::delSystemAdmin($id);
            return true;
        }
        $admin_info = self::find($id)->toArray();
        // 角色信息
        $roles = AdminRoleModel::where('admin_id', $id)->pluck('role_id')->toArray();
        $admin_info['roles'] = $roles;
        // 缓存管理员信息
        AdminCache::setSystemAdmin($id, $admin_info);
        // 更新角色与管理员关联数据
        $roles_all = AdminRoleModel::all();
        $role_admin = $roles_all->groupBy('role_id')->map(function ($item) {
            return $item->pluck('admin_id')->toArray();
        })->toArray();
        AdminCache::setSystemAdminRole($role_admin);
        return true;
    }
}
