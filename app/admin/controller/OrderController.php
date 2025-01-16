<?php

namespace app\admin\controller;

use app\common\cache\PayChannelCache;
use app\common\cache\PayMethodCache;
use app\common\controller\CrudController;
use app\common\model\FeeRuleModel;
use app\common\model\LogModel;
use app\common\model\OrderModel;
use app\common\model\PayChannelModel;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 订单管理
 */
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
     * 订单列表
     * @return Response
     */
    public function index() {
        return view('order/index');
    }

    /**
     * 订单详情
     * @param Request $request
     * @return Response
     */
    public function detail(Request $request): Response {
        $id = $request->input('id');
        $order = $this->model::find($id);
        if ($request->method() === 'GET') {
            if (!$order) {
                return $this->error('error_records');
            }
            $order['fee_rule'] = json_decode($order['fee_rule'], true);
            $order['fee_list'] = json_decode($order['fee_list'], true);
            $order['amount_list'] = json_decode($order['amount_list'], true);
            $order['pay_at'] = $order['pay_at'] ? date('Y-m-d H:i:s', $order['pay_at']) : '';
            // 全部支付通道列表
            $channel_list = PayChannelCache::getList();
            $channel_list = collect($channel_list)->pluck('name', 'id')->toArray();
            loginfo('channel_list', [$channel_list]);
            // 支付方式列表
            $method_list = PayMethodCache::getList();
            $method_list = collect($method_list)->pluck('name', 'id')->toArray();
            loginfo('method_list', [$method_list]);
            // 订单类型列表
            $type_list = C('CATE_ORDER_TYPE');
            loginfo('$type_list', [$type_list]);
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
     * 后台提单
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response {
        if ($request->method() === 'POST') {
            $data = $this->insertInput($request);
            $trade_no = OrderModel::getTradeNo();
            // 后台提单的、商户交易、接口交易号与系统交易号相同
            $data['trade_no'] = $data['out_trade_no'] = $data['api_trade_no'] = $trade_no;
            $data['method_id'] = 8; // 线下支付方式
            $data['type'] = OrderModel::ORDER_TYPE_BACKEND;
            // 分账规则
            $data['fee_rule'] = json_encode(FeeRuleModel::getRules($data['channel_id'], $data['merchant_id']));
            // 手续费分账金额
            $data['fee_list'] = json_encode(FeeRuleModel::getFeeList($data['handling_fee'], $data['channel_id'], $data['merchant_id']));
            // 订单结算金额列表
            $data['amount_list'] = json_encode([$data['merchant_id'] => $data['received_amount']]);
            if ($data['status'] == OrderModel::ORDER_STATUS_PAID) {
                $data['pay_at'] = time();
            }
            $id = $this->doInsert($data);
            // 付款的订单加入到资金变动队列
            if ($data['status'] == OrderModel::ORDER_STATUS_PAID) {
                OrderModel::sendFundQueue($id);
                OrderModel::sendFeeQueue($id);
            }
            $this->output['id'] = $id;
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_ORDER,
                $id,
                '',
                $data
            );
            return $this->success();
        }
        return view('order/insert');
    }

    /**
     * 更新订单
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update(Request $request): Response {
        if ($request->method() === 'GET') {
            return view('order/update');
        }
        [$id, $data] = $this->updateInput($request);
        // 获取$data中的key，作为查询的字段
        $select_field = collect($data)->keys()->toArray();
        $before_data = $this->model->select($select_field)->find($id)->toArray();
        $this->doUpdate($id, $data);
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_ORDER,
            $id,
            $before_data,
            $data
        );
        return $this->success();
    }

    /**
     * 设置订单为已付款
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function setPaid(Request $request): Response {
        [$id, $data] = $this->updateInput($request);
        $before_data = $this->model->select(['id', 'status'])->find($id)->toArray();
        // 订单状态不是未付款，不能设置为已付款
        if ($before_data['status'] != OrderModel::ORDER_STATUS_UNPAID) {
            $this->error('error_order_status_not_unpaid');
        }
        $data['status'] = OrderModel::ORDER_STATUS_PAID; // 设置为已付款
        $data['pay_at'] = time(); // 同时更新付款时间
        $this->doUpdate($id, $data);
        // 从未付款->已付款，加入资金变动队列
        OrderModel::sendFundQueue($id);
        OrderModel::sendFeeQueue($id);
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_ORDER,
            $id,
            $before_data,
            $data
        );
        return $this->success();
    }

    /**
     * 删除订单
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function delete(Request $request): Response {
        $ids = $this->deleteInput($request);
        // 历史数据 以id为key
        $before_data = $this->model->whereIn('id', $ids)->get()->keyBy('id')->toArray();
        $this->doDelete($ids);
        foreach ($ids as $id) {
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_ORDER,
                $id,
                $before_data[$id] ?? '',
                ''
            );
        }
        return $this->success();
    }

    /**
     * 退款
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function refund(Request $request): Response {
        [$id, $data] = $this->updateInput($request);
        $before_data = $this->model->select(['id', 'status', 'amount', 'refund', 'refund_at'])->find($id)->toArray();
        // 订单状态不是已付款，不能退款
        if ($before_data['status'] != OrderModel::ORDER_STATUS_PAID) {
            $this->error('error_order_status_not_paid');
        }
        $data['status'] = OrderModel::ORDER_STATUS_REFUNDED; // 设置为已退款
        $data['refund'] = $data['refund'] ?? $before_data['amount']; // 退款金额
        $data['refund_at'] = time(); // 同时更新退款时间
        $data['received_amount'] = 0; // 到账金额修改为0
        $data['handling_fee'] = 0; // 手续费修改为0
        $this->doUpdate($id, $data);
        // 退款，加入资金变动队列
        OrderModel::sendFundQueue($id);
        OrderModel::sendFeeQueue($id);
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_ORDER,
            $id,
            $before_data,
            $data
        );
        return $this->success();
    }
}