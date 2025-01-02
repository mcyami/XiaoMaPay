<?php

namespace app\admin\controller;

use app\common\model\LogModel;
use app\common\model\RoleModel;
use app\common\model\RuleModel;
use app\common\utils\Auth;
use app\common\utils\Tree;
use support\exception\BusinessException;
use support\Request;
use support\Response;
use Throwable;

/**
 * 角色管理
 */
class RoleController extends CrudController {
    /**
     * 不需要鉴权的方法
     * @var array
     */
    protected $noNeedAuth = ['select'];

    /**
     * @var RoleModel
     */
    protected $model = null;

    /**
     * 构造函数
     */
    public function __construct() {
        $this->model = new RoleModel;
    }

    /**
     * 浏览
     * @return Response
     * @throws Throwable
     */
    public function index(): Response {
        return view('role/index');
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select(Request $request): Response {
        $id = $request->get('id');
        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        $role_ids = Auth::getScopeRoleIds(true);
        if (!$id) {
            $where['id'] = ['in', $role_ids];
        } elseif (!in_array($id, $role_ids)) {
            throw new BusinessException('无权限');
        }
        $query = $this->doSelect($where, $field, $order);
        return $this->doFormat($query, $format, $limit);
    }

    /**
     * 新增角色
     * @param Request $request
     * @return Response
     * @throws BusinessException
     * @throws Throwable
     */
    public function insert(Request $request): Response {
        if ($request->method() === 'POST') {
            $data = $this->insertInput($request);
            $pid = $data['pid'] ?? null;
            if (!$pid) {
                return $this->error('error_no_pid_empty');
            }
            if (!Auth::isSuperAdmin() && !in_array($pid, Auth::getScopeRoleIds(true))) {
                return $this->error('error_no_pid_out_scope');
            }
            $this->checkRules($pid, $data['rules'] ?? '');

            $id = $this->doInsert($data);
            $this->output['id'] = $id;
            RoleModel::cache();
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_ROLE,
                $id,
                '',
                RoleModel::find($id)->toArray()
            );
            return $this->success();
        }
        return view('role/insert');
    }

    /**
     * 更新角色
     * @param Request $request
     * @return Response
     * @throws BusinessException|Throwable
     */
    public function update(Request $request): Response {
        if ($request->method() === 'GET') {
            return view('role/update');
        }
        [$id, $data] = $this->updateInput($request);
        $is_supper_admin = Auth::isSuperAdmin();
        $descendant_role_ids = Auth::getScopeRoleIds();
        if (!$is_supper_admin && !in_array($id, $descendant_role_ids)) {
            return $this->error('error_no_permission_data');
        }

        $role = RoleModel::find($id);

        if (!$role) {
            return $this->error('error_records');
        }
        $before_data = $role;
        $is_supper_role = $role->rules === '*';

        // 超级角色组不允许更改rules pid 字段
        if ($is_supper_role) {
            unset($data['rules'], $data['pid']);
        }

        if (key_exists('pid', $data)) {
            $pid = $data['pid'];
            if (!$pid) {
                return $this->error('error_no_pid_empty');
            }
            if ($pid == $id) {
                return $this->error('error_no_pid_self');
            }
            if (!$is_supper_admin && !in_array($pid, Auth::getScopeRoleIds(true))) {
                return $this->error('error_no_pid_out_scope');
            }
        } else {
            $pid = $role->pid;
        }

        if (!$is_supper_role) {
            $this->checkRules($pid, $data['rules'] ?? '');
        }

        $this->doUpdate($id, $data);

        // 删除所有子角色组中已经不存在的权限
        if (!$is_supper_role) {
            $tree = new Tree(RoleModel::select(['id', 'pid'])->get());
            $descendant_roles = $tree->getDescendant([$id]);
            $descendant_role_ids = array_column($descendant_roles, 'id');
            $rule_ids = $data['rules'] ? explode(',', $data['rules']) : [];
            foreach ($descendant_role_ids as $role_id) {
                $tmp_role = RoleModel::find($role_id);
                $tmp_rule_ids = $role->getRuleIds();
                $tmp_rule_ids = array_intersect(explode(',', $tmp_role->rules), $tmp_rule_ids);
                $tmp_role->rules = implode(',', $tmp_rule_ids);
                $tmp_role->save();
            }
        }
        RoleModel::cache();
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_ROLE,
            $id,
            $before_data->toArray(),
            RoleModel::find($id)->toArray()
        );
        return $this->success();
    }

    /**
     * 删除角色
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function delete(Request $request): Response {
        $ids = $this->deleteInput($request);
        if (in_array(1, $ids)) {
            return $this->error('error_can_not_delete_super_admin');
        }
        if (!Auth::isSuperAdmin() && array_diff($ids, Auth::getScopeRoleIds())) {
            return $this->error('error_no_permission_delete');
        }
        $tree = new Tree(RoleModel::get());
        $descendants = $tree->getDescendant($ids);
        if ($descendants) {
            $ids = array_merge($ids, array_column($descendants, 'id'));
        }
        // 历史数据 以id为key
        $before_data = RoleModel::whereIn('id', $ids)->get()->keyBy('id')->toArray();
        $this->doDelete($ids);
        RoleModel::cache();
        foreach ($ids as $id) {
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_ROLE,
                $id,
                $before_data[$id] ?? '',
                ''
            );
        }
        return $this->success();
    }

    /**
     * 获取角色权限
     * @param Request $request
     * @return Response
     */
    public function rules(Request $request): Response {
        $role_id = $request->get('id');
        if (empty($role_id)) {
            return $this->success();
        }
        if (!Auth::isSuperAdmin() && !in_array($role_id, Auth::getScopeRoleIds(true))) {
            return $this->error('error_role_out_scope');
        }
        $rule_id_string = RoleModel::where('id', $role_id)->value('rules');
        if ($rule_id_string === '') {
            return $this->success();
        }
        $rules = RuleModel::get();
        $include = [];
        if ($rule_id_string !== '*') {
            $include = explode(',', $rule_id_string);
        }
        $items = [];
        foreach ($rules as $item) {
            $items[] = [
                'name' => $item->title ?? $item->name ?? $item->id,
                'value' => (string)$item->id,
                'id' => $item->id,
                'pid' => $item->pid,
            ];
        }
        $tree = new Tree($items);
        $this->output = $tree->getTree($include);
        return $this->success();
    }

    /**
     * 检查权限字典是否合法
     * @param int $role_id
     * @param $rule_ids
     * @return void
     * @throws BusinessException
     */
    protected function checkRules(int $role_id, $rule_ids) {
        if ($rule_ids) {
            $rule_ids = explode(',', $rule_ids);
            if (in_array('*', $rule_ids)) {
                throw new BusinessException(trans('error_invalid_parameter'));
            }
            $rule_exists = RuleModel::whereIn('id', $rule_ids)->pluck('id');
            if (count($rule_exists) != count($rule_ids)) {
                throw new BusinessException(trans('error_permission_no_exists'));
            }
            $rule_id_string = RoleModel::where('id', $role_id)->value('rules');
            if ($rule_id_string === '') {
                throw new BusinessException(trans('error_data_out_scope'));
            }
            if ($rule_id_string === '*') {
                return;
            }
            $legal_rule_ids = explode(',', $rule_id_string);
            if (array_diff($rule_ids, $legal_rule_ids)) {
                throw new BusinessException(trans('error_data_out_scope'));
            }
        }
    }


}
