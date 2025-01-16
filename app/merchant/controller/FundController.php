<?php
namespace app\merchant\controller;

use app\common\controller\CrudController;
use app\common\model\MerchantFundModel;
use app\common\model\MerchantModel;
use support\exception\BusinessException;
use support\Request;
use support\Response;

class FundController extends CrudController {

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
     * 商户资金列表
     * @return Response
     */
    public function index() {
        return view('fund/index');
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select(Request $request): Response {
        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        // 数据隔离 商户只能查看自己的资金记录
        $where['merchant_id'] = MerchantModel::merchantId();
        $query = $this->doSelect($where, $field, $order);
        return $this->doFormat($query, $format, $limit);
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