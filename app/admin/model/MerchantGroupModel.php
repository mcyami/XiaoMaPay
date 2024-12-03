<?php

namespace app\admin\model;

/**
 * 商户组
 * @property integer $id ID(主键)
 * @property string $name 商户组名称
 * @property integer $settle_type 结算方式
 * @property integer $settle_period 结算周期
 * @property float $settle_rate 结算手续费费率
 * @property string $channel_config 商户通道及费率配置
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 * @property integer $sort 排序
 * @property integer $status 状态 1正常 0禁用
 */
class MerchantGroupModel extends BaseModel {
    // 表名称
    protected $table = 'xm_merchant_group';

    // 主键
    protected $primaryKey = 'id';

    // 时间戳存储格式
    protected $dateFormat = 'U';

    const MERCHANT_GROUP_STATUS_ENABLE = 1; // 启用
    const MERCHANT_GROUP_STATUS_DISABLE = 0; // 禁用

}
