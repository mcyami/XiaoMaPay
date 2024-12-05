<?php

namespace app\admin\controller;

use app\admin\model\MerchantFundModel;
use app\admin\model\MerchantModel;
use support\Response;

/**
 * 商户资金变动
 */
class MerchantFundController extends CrudController {

    /**
     * @var MerchantFundModel
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
        $this->model = new MerchantFundModel;
    }

    /**
     * 商户资金明细列表
     * @return Response
     */
    public function index() {
        return view('merchant_fund/index');
    }
}