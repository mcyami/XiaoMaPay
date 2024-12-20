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
            $handling_fee = $orderInfo->handling_fee;
            if ($handling_fee <= 0) {
                Log::info('===consume 订单手续费为0===', [$orderInfo->trade_no]);
                return;
            }
            // 获取该通道所有的分账规则
            $rules = FeeRuleModel::getRulesByChannelId($channel_id);
            // 判断使用通用规则还是商户规则
            $merchant_id = $orderInfo->merchant_id;
            $rule = $rules[$merchant_id] ?? $rules['*'] ?? null;
            if (!$rule) {
                Log::info('===consume 未找到分账规则===', [$orderInfo->trade_no]);
                return;
            }
            // 计算分账金额
            $feeList = [];
            foreach ($rule as $merchant_id => $item) {
                $fee = number_format($handling_fee * $item / 100, 2, '.', '');
                $feeList[] = [
                    'merchant_id' => $merchant_id,
                    'fee' => $fee,
                ];
            }
            Log::info('===consume 分账金额===', $feeList);

            // 分账金额处理
            foreach ($feeList as $item) {
                MerchantModel::changeBalance($item['merchant_id'], MerchantFundModel::FUND_TYPE_ORDER_FEE, $item['fee'], $orderInfo->trade_no);
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
