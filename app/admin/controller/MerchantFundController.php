<?php

namespace app\admin\controller;

use app\admin\model\MerchantFundModel;
use support\Db;
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

    /**
     * 重新通用格式化
     * @param $model
     * @param $items
     * @param $total
     * @return Response
     */
    protected function formatNormal($model, $items, $total): Response {
        $addQuery = clone $model->getQuery();
        $add = $addQuery->where('action', 1)->sum('amount'); // 增加金额

        $subQuery = clone $model->getQuery();
        $sub = $subQuery->where('action', 2)->sum('amount'); // 减少金额

        $this->extra = [
            'amount' => bcsub($add, $sub, 2),
        ];
        $this->output = $items;
        $this->count = $total;
        return $this->success();
    }
}