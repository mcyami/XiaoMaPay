<?php

namespace app\admin\controller;

use app\admin\cache\RuleCache;
use app\admin\model\AdminModel;
use app\admin\model\LogModel;
use app\admin\model\RuleModel;
use app\common\utils\Tree;
use app\common\utils\Util;
use support\exception\BusinessException;
use support\Request;
use support\Response;
use Throwable;

/**
 * 权限菜单
 */
class RuleController extends CrudController {
    /**
     * 不需要权限的方法
     *
     * @var string[]
     */
    protected $noNeedAuth = ['get', 'permission'];

    /**
     * @var RuleModel
     */
    protected $model = null;

    /**
     * 构造函数
     */
    public function __construct() {
        $this->model = new RuleModel;
    }

    /**
     * 浏览
     * @return Response
     * @throws Throwable
     */
    public function index(): Response {
        return view('rule/index');
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select(Request $request): Response {
        $this->syncRules();
        return parent::select($request);
    }

    /**
     * 获取菜单
     * @param Request $request
     * @return Response
     */
    function get(Request $request): Response {
        $rules = RuleModel::getRules(AdminModel::admin('roles'));
        $types = $request->get('type', '0,1');
        $types = is_string($types) ? explode(',', $types) : [0, 1];
        // 只显示开启的菜单
        $items = RuleModel::orderBy('weight', 'desc')
            ->where('status', 1)
            ->get()->toArray();

        $formatted_items = [];
        foreach ($items as $item) {
            $item['pid'] = (int)$item['pid'];
            $item['name'] = $item['title'];
            $item['value'] = $item['id'];
            $item['icon'] = $item['icon'] ? "layui-icon {$item['icon']}" : '';
            $formatted_items[] = $item;
        }

        $tree = new Tree($formatted_items);
        $tree_items = $tree->getTree();
        // 超级管理员权限为 *
        if (!in_array('*', $rules)) {
            $this->removeNotContain($tree_items, 'id', $rules);
        }
        $this->removeNotContain($tree_items, 'type', $types);
        $this->output = $this->empty_filter(Tree::arrayValues($tree_items));
        return $this->success();
    }

    private function empty_filter($menus) {
        return array_map(
            function ($menu) {
                if (isset($menu['children'])) {
                    $menu['children'] = $this->empty_filter($menu['children']);
                }
                return $menu;
            },
            array_values(array_filter(
                $menus,
                function ($menu) {
                    return $menu['type'] != 0 || isset($menu['children']) && count($this->empty_filter($menu['children'])) > 0;
                }
            ))
        );
    }

    /**
     * 获取权限
     * @param Request $request
     * @return Response
     */
    public function permission(Request $request): Response {
        $rules = RuleModel::getRules(AdminModel::admin('roles'));
        // 超级管理员
        if (in_array('*', $rules)) {
            $this->output = ['*'];
            return $this->success();
        }
        // 获取权限规则缓存
        $keys_cache = RuleCache::getSystemRule();
        $permissions = collect($keys_cache)->only($rules)->values()->toArray();
        $this->output = $permissions;
        return $this->success();
    }

    /**
     * 根据类同步规则到数据库
     * @return void
     */
    protected function syncRules() {
        $items = $this->model->where('key', 'like', '%\\\\%')->get()->keyBy('key');
        $methods_in_db = [];
        $methods_in_files = [];
        foreach ($items as $item) {
            $class = $item->key;
            if (strpos($class, '@')) {
                $methods_in_db[$class] = $class;
                continue;
            }
            if (class_exists($class)) {
                $reflection = new \ReflectionClass($class);
                $properties = $reflection->getDefaultProperties();
                $no_need_auth = array_merge($properties['noNeedLogin'] ?? [], $properties['noNeedAuth'] ?? []);
                $class = $reflection->getName();
                $pid = $item->id;
                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    $method_name = $method->getName();
                    // index规则也要加入
                    if (strpos($method_name, '__') === 0 || in_array($method_name, $no_need_auth)) {
                        continue;
                    }
                    $name = "$class@$method_name";

                    $methods_in_files[$name] = $name;
                    $title = Util::getCommentFirstLine($method->getDocComment()) ?: $method_name;
                    $menu = $items[$name] ?? [];
                    if ($menu) {
                        if ($menu->title != $title) {
                            RuleModel::where('key', $name)->update(['title' => $title]);
                        }
                        continue;
                    }
                    $menu = new RuleModel;
                    $menu->pid = $pid;
                    $menu->key = $name;
                    $menu->title = $title;
                    $menu->type = 2;
                    $menu->save();
                }
            }
        }
        // 从数据库中删除已经不存在的方法
        $menu_names_to_del = array_diff($methods_in_db, $methods_in_files);
        if ($menu_names_to_del) {
            //Rule::whereIn('key', $menu_names_to_del)->delete();
        }
    }

    /**
     * 查询前置方法
     * @param Request $request
     * @return array
     * @throws BusinessException
     */
    protected function selectInput(Request $request): array {
        [$where, $format, $limit, $field, $order] = parent::selectInput($request);
        // 允许通过type=0,1格式传递菜单类型
        $types = $request->get('type');
        if ($types && is_string($types)) {
            $where['type'] = ['in', explode(',', $types)];
        }
        // 默认weight排序
        if (!$field) {
            $field = 'weight';
            $order = 'desc';
        }
        return [$where, $format, $limit, $field, $order];
    }

    /**
     * 新增菜单
     * @param Request $request
     * @return Response
     * @throws BusinessException|Throwable
     */
    public function insert(Request $request): Response {
        if ($request->method() === 'GET') {
            return view('rule/insert');
        }
        $data = $this->insertInput($request);
        if (empty($data['type'])) {
            $data['type'] = strpos($data['key'], '\\') ? 1 : 0;
        }
        $data['key'] = str_replace('\\\\', '\\', $data['key']);
        $key = $data['key'] ?? '';
        if ($this->model->where('key', $key)->first()) {
            return $this->error('error_rule_key_exists');
        }
        $data['pid'] = empty($data['pid']) ? 0 : $data['pid'];
        $id = $this->doInsert($data);
        RuleModel::cache();
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_RULE,
            $id,
            '',
            $data
        );
        return $this->success();
    }

    /**
     * 更新菜单
     * @param Request $request
     * @return Response
     * @throws BusinessException|Throwable
     */
    public function update(Request $request): Response {
        if ($request->method() === 'GET') {
            return view('rule/update');
        }

        [$id, $data] = $this->updateInput($request);
        if (!$row = $this->model->find($id)) {
            return $this->error('error_records');
        }
        $before_data = $row->toArray();
        if (isset($data['pid'])) {
            loginfo('pid', ['data'=>$data]);
            $data['pid'] = is_numeric($data['pid']) ? $data['pid'] : 0;
            if ($data['pid'] == $row['id']) {
                return $this->error('error_no_pid_self');
            }
        } else {
            unset($data['pid']);
        }
        if (isset($data['key'])) {
            $data['key'] = str_replace('\\\\', '\\', $data['key']);
        }
        $this->doUpdate($id, $data);
        RuleModel::cache();
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_RULE,
            $id,
            $before_data,
            $this->model->find($id)->toArray()
        );
        return $this->success();
    }

    /**
     * 删除菜单
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response {
        $ids = $this->deleteInput($request);
        // 子规则一起删除
        $delete_ids = $children_ids = $ids;
        while ($children_ids) {
            $children_ids = $this->model->whereIn('pid', $children_ids)->pluck('id')->toArray();
            $delete_ids = array_merge($delete_ids, $children_ids);
        }
        // 历史数据 以id为key的数组
        $before_data = $this->model->whereIn('id', $delete_ids)->get()->keyBy('id')->toArray();
        $this->doDelete($delete_ids);
        RuleModel::cache();
        foreach ($delete_ids as $id) {
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_RULE,
                $id,
                $before_data[$id] ?? '',
                ''
            );
        }
        return $this->success();
    }

    /**
     * 移除不包含某些数据的数组
     * @param $array
     * @param $key
     * @param $values
     * @return void
     */
    protected function removeNotContain(&$array, $key, $values) {
        foreach ($array as $k => &$item) {
            if (!is_array($item)) {
                continue;
            }
            if (!$this->arrayContain($item, $key, $values)) {
                unset($array[$k]);
            } else {
                if (!isset($item['children'])) {
                    continue;
                }
                $this->removeNotContain($item['children'], $key, $values);
            }
        }
    }

    /**
     * 判断数组是否包含某些数据
     * @param $array
     * @param $key
     * @param $values
     * @return bool
     */
    protected function arrayContain(&$array, $key, $values): bool {
        if (!is_array($array)) {
            return false;
        }
        if (isset($array[$key]) && in_array($array[$key], $values)) {
            return true;
        }
        if (!isset($array['children'])) {
            return false;
        }
        foreach ($array['children'] as $item) {
            if ($this->arrayContain($item, $key, $values)) {
                return true;
            }
        }
        return false;
    }

}
