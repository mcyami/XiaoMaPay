<?php

namespace app\admin\controller;

use app\admin\cache\AdminCache;
use app\common\model\AdminModel;
use app\common\model\AdminRoleModel;
use app\common\model\LogModel;
use app\common\utils\Auth;
use support\exception\BusinessException;
use support\Request;
use support\Response;
use Throwable;

/**
 * 管理员列表
 */
class AdminController extends CrudController {
    /**
     * 不需要鉴权的方法
     * @var array
     */
    protected $noNeedAuth = ['select'];

    /**
     * @var AdminModel
     */
    protected $model = null;

    /**
     * 开启auth数据限制
     * @var string
     */
    protected $dataLimit = 'auth';

    /**
     * 以id为数据限制字段
     * @var string
     */
    protected $dataLimitField = 'id';

    /**
     * 构造函数
     * @return void
     */
    public function __construct() {
        $this->model = new AdminModel;
    }

    /**
     * 浏览
     * @return Response
     * @throws Throwable
     */
    public function index(): Response {
        return view('admin/index');
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select(Request $request): Response {
        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        $query = $this->doSelect($where, $field, $order);
        if ($format === 'select') {
            return $this->formatSelect($query->get());
        }
        $paginator = $query->paginate($limit);
        $items = $paginator->items();
        $admin_ids = array_column($items, 'id');
        $roles = AdminRoleModel::whereIn('admin_id', $admin_ids)->get();
        $roles_map = [];
        foreach ($roles as $role) {
            $roles_map[$role['admin_id']][] = $role['role_id'];
        }
        $login_admin_id = AdminModel::adminId();
        foreach ($items as $index => $item) {
            $admin_id = $item['id'];
            $items[$index]['roles'] = isset($roles_map[$admin_id]) ? implode(',', $roles_map[$admin_id]) : '';
            $items[$index]['show_toolbar'] = $admin_id != $login_admin_id;
            $items[$index]['login_time'] = $item['login_at'] ? date('Y-m-d H:i:s', $item['login_at']) : '';
        }
        $this->count = $paginator->total();
        $this->output = $items;
        return $this->success();
    }

    /**
     * 新增管理员
     * @param Request $request
     * @return Response
     * @throws BusinessException|Throwable
     */
    public function insert(Request $request): Response {
        if ($request->method() === 'POST') {
            $data = $this->insertInput($request);
            unset($data['id']);
            $admin_id = $this->doInsert($data);
            $role_ids = $request->post('roles');
            $role_ids = $role_ids ? explode(',', $role_ids) : [];
            if (!$role_ids) {
                return $this->error('error_role_empty');
            }
            if (!Auth::isSuperAdmin() && array_diff($role_ids, Auth::getScopeRoleIds())) {
                return $this->error('error_role_out_scope');
            }
            AdminRoleModel::where('admin_id', $admin_id)->delete();
            foreach ($role_ids as $id) {
                $admin_role = new AdminRoleModel;
                $admin_role->admin_id = $admin_id;
                $admin_role->role_id = $id;
                $admin_role->save();
            }
            $this->output['id'] = $admin_id;
            AdminModel::cache($admin_id);
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_ADMIN,
                $admin_id,
                '',
                AdminCache::getSystemAdmin($admin_id)
            );
            $this->success();
        }
        return view('admin/insert');
    }

    /**
     * 更新管理员
     * @param Request $request
     * @return Response
     * @throws BusinessException|Throwable
     */
    public function update(Request $request): Response {
        if ($request->method() === 'POST') {
            [$id, $data] = $this->updateInput($request);
            $admin_id = $request->post('id');
            $before_data = AdminCache::getSystemAdmin($admin_id); // 修改前数据
            if (!$admin_id) {
                return $this->error('error_lack_param');
            }

            // 不能禁用自己
            if (isset($data['status']) && $data['status'] == 1 && $id == AdminModel::adminId()) {
                return $this->error('error_disable_self');
            }

            // 需要更新角色
            $role_ids = $request->post('roles');
            if ($role_ids !== null) {
                if (!$role_ids) {
                    return $this->error('error_role_empty');
                }
                $role_ids = explode(',', $role_ids);

                $is_supper_admin = Auth::isSuperAdmin();
                $exist_role_ids = AdminRoleModel::where('admin_id', $admin_id)->pluck('role_id')->toArray();
                $scope_role_ids = Auth::getScopeRoleIds();
                if (!$is_supper_admin && !array_intersect($exist_role_ids, $scope_role_ids)) {
                    return $this->error('error_no_permission_edit');
                }
                if (!$is_supper_admin && array_diff($role_ids, $scope_role_ids)) {
                    return $this->error('error_role_out_scope');
                }

                // 删除账户角色
                $delete_ids = array_diff($exist_role_ids, $role_ids);
                AdminRoleModel::whereIn('role_id', $delete_ids)->where('admin_id', $admin_id)->delete();
                // 添加账户角色
                $add_ids = array_diff($role_ids, $exist_role_ids);
                foreach ($add_ids as $role_id) {
                    $admin_role = new AdminRoleModel;
                    $admin_role->admin_id = $admin_id;
                    $admin_role->role_id = $role_id;
                    $admin_role->save();
                }
            }
            $this->doUpdate($id, $data);
            AdminModel::cache($admin_id);
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_ADMIN,
                $admin_id,
                $before_data,
                AdminCache::getSystemAdmin($admin_id)
            );
            return $this->success();
        }

        return view('admin/update');
    }

    /**
     * 删除管理员
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response {
        $primary_key = $this->model->getKeyName();
        $ids = $request->post($primary_key);
        if (!$ids) {
            return $this->success();
        }
        $ids = (array)$ids;
        if (in_array(AdminModel::adminId(), $ids)) {
            return $this->error('error_no_delete_self');
        }
        if (!Auth::isSuperAdmin() && array_diff($ids, Auth::getScopeAdminIds())) {
            return $this->error('error_no_permission_data');
        }
        $this->model->whereIn($primary_key, $ids)->each(function (AdminModel $admin) {
            $admin->delete();
            // 操作日志
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_ADMIN,
                $admin->id,
                AdminCache::getSystemAdmin($admin->id)
            );
            // 清除缓存
            AdminModel::cache($admin->id, true);
        });
        AdminRoleModel::whereIn('admin_id', $ids)->each(function (AdminRoleModel $admin_role) {
            $admin_role->delete();
        });
        return $this->success();
    }

    /**
     * 格式化下拉列表
     * @param $items
     * @return Response
     */
    protected function formatSelect($items): Response {
        $formatted_items = [];
        foreach ($items as $item) {
            $formatted_items[] = [
                'name' => $item->nickname,
                'value' => $item->id
            ];
        }
        $this->output = $formatted_items;
        return $this->success();
    }

}
