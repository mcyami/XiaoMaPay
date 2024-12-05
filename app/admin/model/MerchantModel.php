<?php

namespace app\admin\model;

use support\Db;

/**
 * 商户
 * @property integer $id ID(主键)
 * @property integer $group_id 商户组ID
 * @property integer $type 商户类型 0个人 1企业
 * @property string $username 用户名
 * @property string $password 密码
 * @property string $email 邮箱
 * @property string $phone 手机号掩码
 * @property string $phone_encrypt 手机号密文
 * @property string $qq QQ
 * @property string $url 网址
 * @property string $service 客服联系方式
 * @property string $goods_name 商品名称
 * @property integer $fee_mode 手续费扣除模式 0:商户承担(余额扣减),1:用户承担(订单加费)
 * @property integer $settle_type 结算方式
 * @property string $settle_account 结算账号
 * @property string $settle_name 结算账号名称
 * @property integer $is_auth 是否实名认证 0未认证 1已认证
 * @property string $is_pay 是否开通支付 0禁用 1启用
 * @property string $is_settle 是否开通结算 0禁用 1启用
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 * @property integer $login_at 最后登录时间
 * @property integer $status 状态 1正常 0禁用
 */
class MerchantModel extends BaseModel {
    // 表名称
    protected $table = 'xm_merchant';

    // 主键
    protected $primaryKey = 'id';

    // 时间戳存储格式
    protected $dateFormat = 'U';

    const MERCHANT_STATUS_DISABLE = 0; // 商户状态：禁用
    const MERCHANT_STATUS_ENABLE = 1; // 商户状态：启用
    const MERCHANT_TYPE_PERSONAL = 0; // 商户类型：个人
    const MERCHANT_TYPE_COMPANY = 1; // 商户类型：企业
    const MERCHANT_FEE_MODE_MERCHANT = 0; // 手续费扣除模式：商户承担
    const MERCHANT_FEE_MODE_USER = 1; // 手续费扣除模式：用户承担
    const MERCHANT_AUTH_NO = 0; // 是否实名认证：未认证
    const MERCHANT_AUTH_YES = 1; // 是否实名认证：已认证
    const MERCHANT_PAY_DISABLE = 0; // 是否开通支付：禁用
    const MERCHANT_PAY_ENABLE = 1; // 是否开通支付：启用
    const MERCHANT_SETTLE_DISABLE = 0; // 是否开通结算：禁用
    const MERCHANT_SETTLE_ENABLE = 1; // 是否开通结算：启用

    /**
     * 商户余额变动操作
     * @param $merchant_id
     * @param $type
     * @param $amount
     * @param $trade_no
     * @param string $note
     * @return bool
     */
    public static function changeBalance($merchant_id, $type, $amount, $trade_no, $note = '') {
        // 开启事务
        Db::beginTransaction();
        try {
            $merchant = self::find($merchant_id);
            if (!$merchant) {
                return false;
            }
            $fund = new MerchantFundModel;
            $fund->merchant_id = $merchant_id;
            $fund->type = $type;
            $actions = MerchantFundModel::getFundTypes();
            $fund->action = $actions[$type]['action'];
            $fund->amount = $amount;
            $fund->before_balance = $merchant->balance;
            if ($fund->action == MerchantFundModel::ACTION_ADD) {
                $merchant->balance = bcadd($merchant->balance, $amount, 2);
            }
            if ($fund->action == MerchantFundModel::ACTION_SUB) {
                $merchant->balance = bcsub($merchant->balance, $amount, 2);
            }
            $fund->after_balance = $merchant->balance;
            $fund->trade_no = $trade_no;
            $fund->note = $note;
            $fund->save();
            $merchant->save();
            // 提交事务
            Db::commit();
            return true;
        } catch (\Throwable $e) {
            // 回滚事务
            Db::rollBack();
            return false;
        }
    }

}
