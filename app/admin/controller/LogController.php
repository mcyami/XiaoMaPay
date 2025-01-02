<?php
namespace app\admin\controller;

use app\common\model\LogModel;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 操作日志
 */
class LogController extends CrudController {
    /**
     * @var LogModel
     */
    protected $model = null;

    /**
     * 不需要验证权限的方法
     * @var string[]
     */
    protected $noNeedAuth = [];

    /**
     * 构造函数
     * @return void
     */
    public function __construct() {
        $this->model = new LogModel;
    }

    /**
     * 操作日志列表列表
     * @return Response
     */
    public function index() {
        return view('log/index');
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select(Request $request): Response {
        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        // 倒序
        $field = 'id';
        $order = 'desc';
        $query = $this->doSelect($where, $field, $order);
        if ($format === 'select') {
            return $this->formatSelect($query->get());
        }
        $paginator = $query->paginate($limit);
        $items = $paginator->items();
        // 处理ip
        foreach ($items as &$item) {
            $item['client_ip'] = long2ip($item['client_ip']);
        }
        $this->count = $paginator->total();
        $this->output = $items;
        return $this->success();
    }

}