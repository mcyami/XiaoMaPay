<?php
namespace app\merchant\controller;

use app\common\cache\PayChannelCache;
use app\common\cache\PayMethodCache;
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

    /**
     * 订单详情
     * @param Request $request
     * @return Response
     */
    public function detail(Request $request): Response {
        $id = $request->input('id');
        // 综合订单id和商户id查询，防止越权
        $order = $this->model::where(['id' => $id, 'merchant_id' => MerchantModel::merchantId()])->first();
        if ($request->method() === 'GET') {
            if (!$order) {
                return $this->error('error_records');
            }
            $order = $order->toArray();

            $order['fee_rule'] = json_decode($order['fee_rule'], true);
            $order['fee_list'] = json_decode($order['fee_list'], true);
            $order['amount_list'] = json_decode($order['amount_list'], true);
            $order['pay_at'] = $order['pay_at'] ? date('Y-m-d H:i:s', $order['pay_at']) : '';
            // 全部支付通道列表
            $channel_list = PayChannelCache::getList();
            $channel_list = collect($channel_list)->pluck('name', 'id')->toArray();
            // 支付方式列表
            $method_list = PayMethodCache::getList();
            $method_list = collect($method_list)->pluck('name', 'id')->toArray();
            // 订单类型列表
            $type_list = C('CATE_ORDER_TYPE');
            // 手续费模式
            $fee_mode_list = C('MERCHANT_FEE_MODE');
            // 订单状态
            $status_list = C('CATE_ORDER_STATUS');
            $assign = [
                'order' => $order,
                'channel_list' => $channel_list,
                'method_list' => $method_list,
                'type_list' => $type_list,
                'fee_mode_list' => $fee_mode_list,
                'status_list' => $status_list,
            ];
            return view('order/detail', $assign);
        }
        return $this->success();
    }
}