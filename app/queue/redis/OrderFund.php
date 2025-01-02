<?php

namespace app\queue\redis;

use app\common\model\MerchantFundModel;
use app\common\model\MerchantModel;
use app\common\model\OrderModel;
use support\Log;
use Webman\RedisQueue\Consumer;

/**
 * 订单新增资金变动记录
 */
class OrderFund implements Consumer {
    // 要消费的队列名
    public $queue = OrderModel::QUEUE_ORDER_FUND;

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接
    public $connection = 'default';

    // 消费
    public function consume($data) {
        $order_id = $data['order_id'];
        $orderInfo = OrderModel::find($order_id);
        if (!$orderInfo) {
            Log::error('===consume订单不存在===', ['order_id' => $order_id]);
            return;
        }
        // 根据订单的支付状态，进行资金变动记录
        if ($orderInfo->status == OrderModel::ORDER_STATUS_PAID) {
            // 订单已支付
            // 1- 平台通道，商户余额增加，增加金额为订单实际到账金额
            if ($orderInfo->sub_channel_id == 0) {
                MerchantModel::changeBalance($orderInfo->merchant_id, MerchantFundModel::FUND_TYPE_ORDER_INCOME, $orderInfo->received_amount, $orderInfo->trade_no, $orderInfo->note);
            } else {
                // 2- 钱进到商户通道，商户余额减去订单的手续费
                MerchantModel::changeBalance($orderInfo->merchant_id, MerchantFundModel::FUND_TYPE_ORDER_SERVICE, $orderInfo->handling_fee, $orderInfo->trade_no, $orderInfo->note);
            }
        } else if($orderInfo->status == OrderModel::ORDER_STATUS_REFUNDED) {
            // 订单退款 扣减商户余额
            $amount_list = json_decode($orderInfo->amount_list, true);
            foreach ($amount_list as $m_id => $m_amount) {
                MerchantModel::changeBalance($m_id, MerchantFundModel::FUND_TYPE_ORDER_REFUND, $m_amount, $orderInfo->trade_no, $orderInfo->note);
            }
        }
        Log::info('===consume 订单处理成功===', $orderInfo->toArray());
    }

    public function onConsumeFailure(\Throwable $e, $package) {
        echo "consume failure\n";
        echo $e->getMessage() . "\n";
        Log::info('===consume 订单处理失败===', [$e->getMessage(), $package]);
    }
}
