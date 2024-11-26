<?php

namespace app\admin\controller;

use app\admin\model\LogModel;
use app\admin\model\PayDriverModel;
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

}