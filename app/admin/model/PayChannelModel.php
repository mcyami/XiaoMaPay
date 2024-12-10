<?php

namespace app\admin\model;

use app\admin\cache\PayChannelCache;

/**
 * 支付通道
 * @property integer $id ID(主键)
 * @property integer $mode 通道模式{0:平台通道 1:商户通道}
 * @property integer $method_id 支付方式ID
 * @property string $name 通道显示名称
 * @property string $driver_key 支付驱动标识
 * @property float $ratio 商户结算比率
 * @property string $app_type 可用接口
 * @property float $day_limit 单日限额，0或留空为没有单日限额，超出限额会暂停使用该通道
 * @property integer $day_status 当日状态
 * @property float $pay_min 单笔最小金额
 * @property float $pay_max 单笔最大金额
 * @property integer $app_wxmp 绑定的微信公众号ID
 * @property integer $app_wxa 绑定的微信小程序ID
 * @property float $cost_ratio 通道成本比例
 * @property string $secret_config 密钥配置
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 * @property integer $sort 排序
 * @property integer $status 状态 1正常 0禁用
 */
class PayChannelModel extends BaseModel {
    // 表名称
    protected $table = 'xm_pay_channel';

    // 主键
    protected $primaryKey = 'id';

    // 时间戳存储格式
    protected $dateFormat = 'U';

    const PAY_CHANNEL_STATUS_ENABLE = 1; // 启用
    const PAY_CHANNEL_STATUS_DISABLE = 0; // 禁用

    const PAY_CHANNEL_MODE_PLATFORM = 0; // 平台通道 平台代收
    const PAY_CHANNEL_MODE_MERCHANT = 1; // 商户通道 商户直清

    /**
     * 缓存所有支付驱动
     * @return bool
     */
    public static function cache() {
        // 缓存所有支付驱动
        $payChannels = self::get()->keyBy('id')->toArray();
        PayChannelCache::setList($payChannels);
        return true;
    }
}
