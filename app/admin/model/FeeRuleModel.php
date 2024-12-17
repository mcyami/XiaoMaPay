<?php

namespace app\admin\model;

use app\admin\cache\FeeRuleCache;

/**
 * 手续费分账规则
 * @property integer $id 主键(主键)
 * @property integer $channel_id 渠道ID
 * @property integer $merchant_id 订单收款商户ID
 * @property float $limit_amount 订单最小金额
 * @property integer $receive_merchant_id 分账商户ID
 * @property float $rate 分账百分比
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property integer $status 状态 1启用 0禁用
 */
class FeeRuleModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'xm_fee_rule';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    // 时间戳存储格式
    protected $dateFormat = 'U';

    /**
     * 缓存所有规则
     * 新增、编辑、删除后需要进行缓存更新
     * @return bool
     */
    public static function cache(): bool {
        $rules = self::get()->keyBy('id')->toArray();
        FeeRuleCache::setList($rules);
        return true;
    }

}
