<?php

namespace app\admin\model;

/**
 * 商户资金变动记录
 * @property integer $id ID(主键)
 * @property string $trade_no 系统交易单号
 * @property string $out_trade_no 商户交易单号
 * @property string $api_trade_no 三方支付交易号
 * @property integer $merchant_id 商户ID
 * @property integer $channel_id 支付通道ID
 * @property integer $method_id 支付方式ID
 * @property integer $type 订单类型{0:普通订单,1:聚合收款码,2:充值余额,3:后台提单(线下收款)}
 * @property string $goods_name 商品名称
 * @property float $goods_price 商品价格
 * @property float $procedure_fee 手续费
 * @property float $amount 订单金额(实际支付金额)
 * @property integer $pay_at 支付时间
 * @property float $refund 退款金额
 * @property integer $refund_at 退款时间
 * @property string $note 备注
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

}
