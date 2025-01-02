<?php

namespace app\admin\controller;

use app\common\cache\PayDriverCache;
use app\common\controller\CrudController;
use app\common\model\PayDriverModel;
use app\common\model\PayMethodModel;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 支付驱动管理
 */
class PayDriverController extends CrudController {

    /**
     * @var PayDriverModel
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
        $this->model = new PayDriverModel;
    }

    /**
     * 支付驱动列表
     * @return Response
     */
    public function index() {
        return view('pay_driver/index');
    }

    /**
     * 刷新支付驱动列表
     * @param Request $request
     * @return Response
     */
    public function refresh(Request $request): Response {
        PayDriverModel::reload();
        return $this->success();
    }

    /**
     * 通过支付方式获取支持的驱动列表
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function getListByMethod(Request $request): Response {
        $methodId = $request->input('method_id');
        if (empty($methodId)) {
            $this->error(trans('error_pay_method_id_empty'));
        }
        // 支付方式信息
        $method = PayMethodModel::find($methodId);
        // 获取支付方式可用的驱动列表
        $methodDriverList = PayDriverCache::getMethodDriver();
        $this->output = $methodDriverList[$method['key']] ?? [];
        return $this->success();
    }

}