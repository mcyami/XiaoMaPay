<?php

namespace app\admin\controller;

use app\admin\model\OptionModel;
use app\common\model\ConfigModel;
use app\common\model\LogModel;
use app\common\utils\Util;
use support\exception\BusinessException;
use support\Request;
use support\Response;
use Throwable;

/**
 * 系统设置
 */
class ConfigController extends CrudController {

    /**
     * @var ConfigModel
     */
    protected $model = null;

    /**
     * 不需要验证权限的方法
     * @var string[]
     */
    protected $noNeedAuth = ['get', 'getConfig'];

    /**
     * 构造函数
     * @return void
     */
    public function __construct() {
        $this->model = new ConfigModel; // 配置表模型
    }

    /**
     * 配置管理
     * @return Response
     */
    public function list() {
        return view('config/list');
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
        $this->count = $paginator->total();
        $this->output = $items;
        return $this->success();
    }

    /**
     * 获取指定配置值
     * 数组类型以字典形式返回 [name=>text, value=>1]
     * @param array $items
     * @return Response
     */
    public function getConfig(Request $request) {
        $key = $request->get('key', 'SYS_CONFIG_TYPE');
        $value = C($key);
        if(is_array($value)){
            $selects = [];
            foreach ($value as $key => $val) {
                $selects[] = ['name' => $val, 'value' => $key];
            }
            $value = $selects;
        }
        $this->output = $value;
        return $this->success();
    }

    /**
     * 新增配置项
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response {
        if ($request->method() === 'POST') {
            $data = $this->insertInput($request);
            // KEY 不能重复
            if ($this->doSelect(['key' => $data['key']])->count() > 0) {
                return $this->error('error_record_exists');
            }
            $id = $this->doInsert($data);
            ConfigModel::cache();
            $this->output['id'] = $id;
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_CONFIG,
                $id,
                '',
                $data
            );
            return $this->success();
        }
        return view('config/insert');
    }

    /**
     * 更新配置项
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update(Request $request): Response {
        if ($request->method() === 'GET') {
            return view('config/update');
        }
        [$id, $data] = $this->updateInput($request);
        // KEY 修改了，不能重复
        if (isset($data['key']) && $this->doSelect(['key' => $data['key']])->where('id', '<>', $id)->count() > 0) {
            return $this->error('error_config_key_exists');
        }
        $before_data = $this->model->find($id)->toArray();
        $this->doUpdate($id, $data);
        ConfigModel::cache();
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_CONFIG,
            $id,
            $before_data,
            $data
        );
        return $this->success();
    }

    /**
     * 删除配置项
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function delete(Request $request): Response {
        $ids = $this->deleteInput($request);
        // 历史数据 以id为key
        $before_data = $this->model->whereIn('id', $ids)->get()->keyBy('id')->toArray();
        $this->doDelete($ids);
        ConfigModel::cache();
        foreach ($ids as $id) {
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_CONFIG,
                $id,
                $before_data[$id] ?? '',
                ''
            );
        }
        return $this->success();
    }

    /**
     * 系统配置
     * @return Response
     */
    public function index() {
        // 获取所有配置项 指定查询条件
        $configs = $this->model
            ->where('status', ConfigModel::CONFIG_STATUS_ENABLE)
            ->select('key', 'name', 'type', 'group', 'val', 'extra', 'desc', 'sort_order')
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->transform(function ($item) {
                $item['extra'] = $item['extra'] ? ConfigModel::parseExtra($item['extra']) : '';
                return $item;
            })->toArray();
        $groups = C('SYS_CONFIG_GROUP');
        return view('config/index', ['configs' => $configs, 'groups' => $groups]);
    }

    /**
     * 保存系统配置
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response {
        $data = $request->post();
        $configs = ConfigModel::getConfig();
        $before_data = C();
        foreach ($data as $key => $value) {
            if (isset($configs[$key])) {
                $this->model->updateVal($key, $value);
            }
        }
        ConfigModel::cache();
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_CONFIG,
            0,
            $before_data,
            C()
        );
        return $this->success();
    }

    /**
     * 获取后台配置
     * @return Response
     */
    public function get(): Response {
        $config = $this->getByDefault();
        $config['logo']['title'] = C('SYS_SITE_NAME') ?? $config['logo']['title'];
        $config['logo']['image'] = C('SYS_SITE_LOGO') ?? $config['logo']['image'];
        $config['logo']['footer_txt'] = C('SYS_SITE_FOOTER') ?? $config['logo']['footer_txt'];
        $config['tab']['index']['title'] = C('SYS_DASHBOARD_NAME') ?? $config['tab']['index']['title'];
        return json($config);
    }

    /**
     * 获取后台默认配置
     * @return array
     */
    public function getByDefault(): array {
        return [
            "logo" => [
                "title" => "Webmin",
                "image" => "/admin/images/logo.png",
                "icp" => "",
                "beian" => "",
                "footer_txt" => ""
            ],
            "menu" => [
                "data" => "/admin/rule/get",
                "accordion" => false,
                "collapse" => false,
                "control" => false,
                "controlWidth" => 2000,
                "select" => 0,
                "async" => true
            ],
            "tab" => [
                "enable" => true,
                "keepState" => true,
                "preload" => false,
                "session" => true,
                "max" => "30",
                "index" => [
                    "id" => "0",
                    "href" => "/admin/index/dashboard",
                    "title" => "仪表盘"
                ]
            ],
            "theme" => [
                "defaultColor" => "2",
                "defaultMenu" => "light-theme",
                "defaultHeader" => "light-theme",
                "allowCustom" => true,
                "banner" => false
            ],
            "colors" => [
                ["id" => "1", "color" => "#36b368", "second" => "#f0f9eb"],
                ["id" => "2", "color" => "#2d8cf0", "second" => "#ecf5ff"],
                ["id" => "3", "color" => "#f6ad55", "second" => "#fdf6ec"],
                ["id" => "4", "color" => "#f56c6c", "second" => "#fef0f0"],
                ["id" => "5", "color" => "#3963bc", "second" => "#ecf5ff"]
            ],
            "other" => ["keepLoad" => "500", "autoHead" => false, "footer" => false],
            "header" => ["message" => false]
        ];
    }

    /**
     * 旧配置管理(废弃的)
     * @deprecated
     * @return Response
     * @throws Throwable
     */
    public function oldIndex(): Response {
        return raw_view('config/old_index');
    }

    /**
     * 旧配置更改(废弃的)
     * @deprecated
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function oldSave(Request $request): Response {
        $post = $request->post();
        $config = $this->getByDefault();
        $data = [];
        foreach ($post as $section => $items) {
            if (!isset($config[$section])) {
                continue;
            }
            switch ($section) {
                case 'logo':
                    $data[$section]['title'] = htmlspecialchars($items['title'] ?? '');
                    $data[$section]['image'] = Util::filterUrlPath($items['image'] ?? '');
                    $data[$section]['icp'] = htmlspecialchars($items['icp'] ?? '');
                    $data[$section]['beian'] = htmlspecialchars($items['beian'] ?? '');
                    $data[$section]['footer_txt'] = htmlspecialchars($items['footer_txt'] ?? '');
                    break;
                case 'menu':
                    $data[$section]['data'] = Util::filterUrlPath($items['data'] ?? '');
                    $data[$section]['accordion'] = !empty($items['accordion']);
                    $data[$section]['collapse'] = !empty($items['collapse']);
                    $data[$section]['control'] = !empty($items['control']);
                    $data[$section]['controlWidth'] = (int)($items['controlWidth'] ?? 2000);
                    $data[$section]['select'] = (int)$items['select'] ?? 0;
                    $data[$section]['async'] = true;
                    break;
                case 'tab':
                    $data[$section]['enable'] = true;
                    $data[$section]['keepState'] = !empty($items['keepState']);
                    $data[$section]['preload'] = !empty($items['preload']);
                    $data[$section]['session'] = !empty($items['session']);
                    $data[$section]['max'] = Util::filterNum($items['max'] ?? '30');
                    $data[$section]['index']['id'] = Util::filterNum($items['index']['id'] ?? '0');
                    $data[$section]['index']['href'] = Util::filterUrlPath($items['index']['href'] ?? '');
                    $data[$section]['index']['title'] = htmlspecialchars($items['index']['title'] ?? '首页');
                    break;
                case 'theme':
                    $data[$section]['defaultColor'] = Util::filterNum($items['defaultColor'] ?? '2');
                    $data[$section]['defaultMenu'] = $items['defaultMenu'] ?? '' == 'dark-theme' ? 'dark-theme' : 'light-theme';
                    $data[$section]['defaultHeader'] = $items['defaultHeader'] ?? '' == 'dark-theme' ? 'dark-theme' : 'light-theme';
                    $data[$section]['allowCustom'] = !empty($items['allowCustom']);
                    $data[$section]['banner'] = !empty($items['banner']);
                    break;
                case 'colors':
                    foreach ($config['colors'] as $index => $item) {
                        if (!isset($items[$index])) {
                            $config['colors'][$index] = $item;
                            continue;
                        }
                        $data_item = $items[$index];
                        $data[$section][$index]['id'] = $index + 1;
                        $data[$section][$index]['color'] = $this->filterColor($data_item['color'] ?? '');
                        $data[$section][$index]['second'] = $this->filterColor($data_item['second'] ?? '');
                    }
                    break;

            }
        }
        $config = array_merge($config, $data);
        $name = 'system_config';
        OptionModel::where('name', $name)->update([
            'value' => json_encode($config)
        ]);
        return $this->json(0);
    }

    /**
     * 颜色检查(废弃的)
     * @deprecated
     * @param string $color
     * @return string
     * @throws BusinessException
     */
    protected function filterColor(string $color): string {
        if (!preg_match('/\#[a-zA-Z]6/', $color)) {
            throw new BusinessException('参数错误');
        }
        return $color;
    }

}
