<?php

namespace app\admin\model;

use app\admin\cache\PayChannelCache;
use app\admin\cache\PayMethodCache;
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

    /**
     * 获取商户通道费率
     * @param $merchant_id
     * @param $channel_id
     * @param $method_key
     * @return array
     */
    public static function getChannelRate($merchant_id, $method_key = '', $channel_id = 0) {
        // 商户信息
        $merchant = MerchantModel::find($merchant_id);
        // 商户组信息
        $group = MerchantGroupModel::find($merchant->group_id);
        // 商户组通道费率
        $group_channel_config = json_decode($group->channel_config, true) ?? [];
        // 支付方式列表
        $payMethods = PayMethodCache::getList();
        // 平台通道列表
        $payChannels = PayChannelCache::getList();

        $channelRateList = [];
        foreach ($payMethods as $payMethod) {
            // 去除禁用的支付方式
            if ($payMethod['status'] != PayMethodModel::PAY_METHOD_STATUS_ENABLE) {
                continue;
            }
            $item = [
                'status' => 1, // 开启状态
                'channels' => [], // 平台通道列表
                'channels_rate' => [], // 平台通道费率列表
                'sub_channels' => [], // 商户通道列表
                'sub_channels_rate' => [], // 商户通道费率列表
            ];
            // 商户组是否开启该支付方式 0:关闭 -1:随机平台通道 -2:随机商户通道 >0:指定平台通道
            if (!isset($group_channel_config[$payMethod['id']])) {
                // 没有配置改支付方式
                $item['enabled'] = 0;
                continue;
            }
            $channel_config = $group_channel_config[$payMethod['id']] ?? [];
            $channel_config_type = $channel_config['type'] ?? 0;
            $channel_config_rate = $channel_config['rate'] ?? 0; // 不存在则查询通道默认费率
            if ($channel_config_type == 0) {
                // 配置关闭通道
                $item['enabled'] = 0;
                continue;
            }

            // 1-随机平台通道，将平台通道列表全部加入到 channels
            if ($channel_config_type == -1) {
                // 查询平台通道列表
                foreach ($payChannels as $k => $channelInfo) {
                    $temp_rate = 0;
                    if (
                        $channelInfo['method_id'] != $payMethod['id']
                        || $channelInfo['mode'] != PayChannelModel::PAY_CHANNEL_MODE_PLATFORM
                        || $channelInfo['status'] != PayChannelModel::PAY_CHANNEL_STATUS_ENABLE
                    ) {
                        continue;
                    }
                    $temp_rate = $channel_config_rate;
                    if (empty($channel_config_rate) || $channel_config_rate == 0) {
                        $temp_rate = $channelInfo['ratio'];
                    }
                    $item['channels'][$k] = $channelInfo['name'];
                    $item['channels_rate'][$k] = $temp_rate;
                }
            }

            // 随机商户通道，将商户通道列表全部加入到 sub_channels TODO
//            if($channel_config_type == -2){
//
//            }

            // 3-指定平台通道加入到 channels
            if ($channel_config_type > 0) {
                // $channel_config_rate 不存在则查询通道默认费率
                if ($channel_config_rate == 0) {
                    // 查询通道默认费率 只获取ratio字段
                    $channel_config_rate = PayChannelModel::where('id', $channel_config_type)->pluck('ratio');
                }
                $item['channels'][$channel_config_type] = $payChannels[$channel_config_type]['name'];
                $item['channels_rate'][$channel_config_type] = $channel_config_rate;
            }
            $channelRateList[$payMethod['key']] = $item;
        }

        if ($method_key) {
            if ($channel_id) {
                $return = $channelRateList[$method_key]['channels'][$channel_id] ?? 0;
            } else {
                $return[$method_key] = $channelRateList[$method_key];
            }
        } else {
            $return = $channelRateList;
        }
        return $return;
    }
}
