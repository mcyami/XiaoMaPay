<?php

namespace app\admin\model;

/**
 * 商户资金变动记录
 * @property integer $id ID(主键)
 * @property integer $merchant_id 商户ID
 * @property integer $type 变动类型
 * @property integer $action 记账方向 1:增加 2:减少
 * @property float $amount 变动金额
 * @property float $before_balance 变动前余额
 * @property float $after_balance 变动后余额
 * @property string $trade_no 交易单号
 * @property string $note 备注
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 */
class MerchantFundModel extends BaseModel {
    // 表名称
    protected $table = 'xm_merchant_fund';

    // 主键
    protected $primaryKey = 'id';

    // 时间戳存储格式
    protected $dateFormat = 'U';

    const FUND_TYPE_RECHARGE = 1; // 后台充值
    const FUND_TYPE_DEDUCT = 2; // 后台扣款
    const FUND_TYPE_ORDER_INCOME = 3; // 订单收入
    const FUND_TYPE_ORDER_REFUND = 4; // 订单退款
    const FUND_TYPE_SETTLE = 5; // 自动结算
    const FUND_TYPE_WITHDRAW = 6; // 手动提现
    const FUND_TYPE_MERCHANT_RECHARGE = 7; // 商户充值
    const FUND_TYPE_ORDER_SERVICE = 8; // 订单服务费（商户直清通道，每笔订单手续费扣除的类型）
    const FUND_TYPE_ORDER_FEE = 9; // 订单手续费收入
    const FUND_TYPE_ORDER_FEE_REFUND = 10; // 订单手续费退回
    const FUND_TYPE_ORDER_SERVICE_REFUND = 11; // 订单服务费退回

    const ACTION_ADD = 1; // 增加
    const ACTION_SUB = 2; // 减少

    /**
     * 获取系统资金变动类型
     * @return array
     */
    public static function getFundTypes() {
        // 从系统配置获取
        $typeName = C('CATE_FUND_TYPE');
        $typeAction = C('CATE_FUND_ACTION');
        $typeMap = [];
        foreach ($typeName as $key => $name) {
            $item = [];
            $item['name'] = $name;
            $item['action'] = $typeAction[$key];
            $typeMap[$key] = $item;
        }
        return $typeMap;
    }

}
