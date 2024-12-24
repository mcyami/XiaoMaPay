<?php

namespace app\queue\redis;

use app\admin\model\FeeRuleModel;
use app\admin\model\MerchantFundModel;
use app\admin\model\MerchantModel;
use app\admin\model\OrderModel;
use support\Log;
use Webman\RedisQueue\Consumer;

/**
 * 订单手续费分账处理
 */
class OrderFee implements Consumer {
    // 要消费的队列名
    public $queue = OrderModel::QUEUE_ORDER_FEE;

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
            $channel_id = $orderInfo->channel_id;
            $merchant_id = $orderInfo->merchant_id;
            $handling_fee = $orderInfo->handling_fee;
            $fee_rule = json_decode($orderInfo->fee_rule, true);
            $fee_list = json_decode($orderInfo->fee_list, true);
            if ($handling_fee <= 0) {
                Log::info('===consume 订单手续费为0===', [$orderInfo->trade_no]);
                return;
            }
            Log::info('===consume 分账金额列表===', ['fee_rule' => $fee_rule, 'fee_list' => $fee_list]);

            // 分账金额处理
            foreach ($fee_list as $m_id => $m_fee) {
                MerchantModel::changeBalance($m_id, MerchantFundModel::FUND_TYPE_ORDER_FEE, $m_fee, $orderInfo->trade_no);
            }
        } else if ($orderInfo->status == OrderModel::ORDER_STATUS_REFUNDED) {
            // 订单退款 手续费根据分账规则 fee_list 退回
            $fee_list = json_decode($orderInfo->fee_list, true);
            if (empty($fee_list)) {
                Log::info('===consume 订单手续费分账金额列表为空===', [$orderInfo->trade_no]);
                return;
            }
            foreach ($fee_list as $m_id => $m_fee) {
                MerchantModel::changeBalance($m_id, MerchantFundModel::FUND_TYPE_ORDER_FEE_REFUND, $m_fee, $orderInfo->trade_no);
            }
        }
        Log::info('===consume 订单手续费分账处理成功===', [$orderInfo->trade_no]);
    }

    public function onConsumeFailure(\Throwable $e, $package) {
        echo "consume failure\n";
        echo $e->getMessage() . "\n";
        Log::info('===consume 订单手续费分账处理失败===', [$e->getMessage(), $package]);
    }
}
