<?php

namespace app\admin\model;

use app\common\service\SafetyLockService;
use Webman\RedisQueue\Redis;

/**
 * 商户资金变动记录
 * @property integer $id ID(主键)
 * @property string $trade_no 系统交易单号
 * @property string $out_trade_no 商户交易单号
 * @property string $api_trade_no 三方支付交易号
 * @property integer $merchant_id 商户ID
 * @property integer $channel_id 平台支付通道ID
 * @property integer $sub_channel_id 商户支付通道ID
 * @property integer $method_id 支付方式ID
 * @property integer $type 订单类型{0:普通订单,1:聚合收款码,2:充值余额,3:后台提单(线下收款)}
 * @property string $goods_name 商品名称
 * @property float $goods_price 商品价格
 * @property float $handling_fee 手续费
 * @property float $amount 订单金额(实际支付金额)
 * @property float $received_amount 实际到账金额
 * @property integer $fee_mode 手续费模式{0:商户承担,1:用户承担}
 * @property integer $pay_at 支付时间
 * @property float $refund 退款金额
 * @property integer $refund_at 退款时间
 * @property string $note 备注
 * @property string $fee_rule 手续费分账规则
 * @property string $fee_list 手续费分账金额列表
 * @property string $amount_list 分账金额列表
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 * @property integer $status 订单状态{0:未支付,1:已支付,2:已退款,3:已冻结}
 */
class OrderModel extends BaseModel {
    // 表名称
    protected $table = 'xm_order';

    // 主键
    protected $primaryKey = 'id';

    // 时间戳存储格式
    protected $dateFormat = 'U';

    const ORDER_STATUS_UNPAID = 0; // 未支付
    const ORDER_STATUS_PAID = 1; // 已支付
    const ORDER_STATUS_REFUNDED = 2; // 已退款
    const ORDER_STATUS_FROZEN = 3; // 已冻结

    const ORDER_TYPE_NORMAL = 0; // 普通订单
    const ORDER_TYPE_QRCODE = 1; // 聚合收款码
    const ORDER_TYPE_RECHARGE = 2; // 充值余额
    const ORDER_TYPE_BACKEND = 3; // 后台提单(线下收款)

    const QUEUE_ORDER_FUND = '_order_fund_'; // 订单资金变动队列
    const QUEUE_ORDER_FEE = '_order_fee_'; // 订单手续费分账处理
    const LOCK_ORDER_TRADE_NO = 'system:order_trade_no:'; // 交易单号锁

    /**
     * 订单加入到资金变动队列
     * @param integer $order_id 订单ID
     * @return boolean
     */
    public static function sendFundQueue($order_id) {
        $queueName = self::QUEUE_ORDER_FUND;
        $queueData = [
            'order_id' => $order_id,
        ];
        return Redis::send($queueName, $queueData);
    }

    /**
     * 订单加入到手续费分账队列
     * @param integer $order_id 订单ID
     * @return boolean
     */
    public static function sendFeeQueue($order_id) {
        $queueName = self::QUEUE_ORDER_FEE;
        $queueData = [
            'order_id' => $order_id,
        ];
        return Redis::send($queueName, $queueData);
    }

    /**
     * 生成系统唯一的交易单号 20位
     * @return string
     */
    public static function getTradeNo() {
        while (true) {
            $tradeNo = date('YmdHis') . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            // 验证唯一性
            $key = self::LOCK_ORDER_TRADE_NO . $tradeNo;
            if (SafetyLockService::addLock($key, 120)) {
                break;
            }
        }
        return $tradeNo;
    }
}
