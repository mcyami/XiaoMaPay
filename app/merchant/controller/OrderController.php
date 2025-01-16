<?php
namespace app\merchant\controller;

use app\common\controller\CrudController;
use app\common\model\MerchantModel;
use app\common\model\OrderModel;
use support\exception\BusinessException;
use support\Request;
use support\Response;

class OrderController extends CrudController {

    /**
     * @var OrderModel
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
        $this->model = new OrderModel;
    }

    /**
     * 商户订单记录
     * @return Response
     */
    public function index(Request $request): Response {
        return view('order/index');
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select(Request $request): Response {
        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        // 数据隔离 商户只能查看自己的订单
        $where['merchant_id'] = MerchantModel::merchantId();
        $query = $this->doSelect($where, $field, $order);
        return $this->doFormat($query, $format, $limit);
    }

    /**
     * 通用格式化
     * @param $model
     * @param $items
     * @param $total
     * @return Response
     */
    protected function formatNormal($model, $items, $total): Response {
        $this->extra = [
            'amount' => $model->sum('amount'), // 订单金额
            'received_amount' => $model->sum('received_amount'), // 到账金额
            'refund' => $model->sum('refund'), // 退款金额
            'handling_fee' => $model->sum('handling_fee'), // 手续费
            'goods_price' => $model->sum('goods_price'), // 商品价格
        ];
        foreach ($items as &$item) {
            $item['pay_at'] = $item['pay_at'] ? date('Y-m-d H:i:s', $item['pay_at']) : '';
            $item['refund_at'] = $item['refund_at'] ? date('Y-m-d H:i:s', $item['refund_at']) : '';
        }
        $this->output = $items;
        $this->count = $total;
        return $this->success();
    }
}