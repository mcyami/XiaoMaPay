<?php

namespace app\common\model;

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

    /**
     * 计算通道手续费分账金额列表
     * @param float $fee 手续费
     * @param int $channelId 通道ID
     * @param int $merchantId 获取指定商户ID 0获取通道所有规则 *获取通道通用规则 >0获取指定商户规则
     * @return array
     */
    public static function getFeeList(float $fee, int $channelId, int $merchantId = 0): array {
        $rules = self::getRules($channelId, $merchantId);
//        loginfo('===Rules===', $rules);
        $rates = [];
        foreach ($rules as $merchantId => $rule) {
            $rates[$merchantId] = number_format($fee * $rule / 100, 2, '.', '');
        }
        return $rates;
    }

    /**
     * 获取通道手续费分账比例
     * @param int $channelId 通道ID
     * @param int $merchantId 获取指定商户ID 0获取通道所有规则 *获取通道通用规则 >0获取指定商户规则
     * @return array
     */
    public static function getRules(int $channelId, int $merchantId = 0): array {
        // 获取该通道所有的分账规则
        $rules = self::where('channel_id', $channelId)->get();
        // 通过merchant_id分组
        $rules = $rules->groupBy('merchant_id')->toArray();
        $channelRules = [];
        // 平台默认手续费收款商户ID
        $platformMerchantId = C('PLATFORM_FEE_MERCHANT');

        /* 通过规则生成分账比例 */
        // 1、生成通道通用分账比例数组
        $cent = 100;
        $common_rule = []; // 通用规则
        $total = 0; // 规则中设置的比例总和
        if (isset($rules[0]) && !empty($rules[0])) {
            foreach ($rules[0] as $r) {
                $total = bcadd($total, $r['rate'], 2);
                $common_rule[$r['receive_merchant_id']] = $r['rate'];
            }
            // 不足100%的部分，平台收取
            if ($total < $cent) {
                $common_rule[$platformMerchantId] = bcsub($cent, $total, 2);
            }
        } else {
            // 通道没有设置分账规则，平台收取全部
            $common_rule[$platformMerchantId] = $cent;
        }
        $channelRules["*"] = $common_rule;

        // 2、生成指定商户分账比例，基于通用分账比例计算，中间人分账从指定平台商户ID中扣去
        if (count($rules) > 1) {
            foreach ($rules as $mId => $rule) {
                if ($mId == 0) {
                    continue;
                }
                $merchant_rule = $common_rule;
                foreach ($rule as $r) {
                    $nowPlatformId = $r['platform_id'];
                    $nowPlatformRate = $merchant_rule[$nowPlatformId] ?? 0;
                    // 指定平台商户ID在通用规则中的分成 > 当前中间人分成，则平台商户ID分成减去中间人分成，减去部分给中间人
                    if ($nowPlatformRate) {
                        if ($nowPlatformRate > $r['rate']) {
                            // 平台分成 > 中间人分成，平台分成中减掉中间人分成，加入中间人分成
                            $merchant_rule[$nowPlatformId] = bcsub($nowPlatformRate, $r['rate'], 2);
                            $merchant_rule[$r['receive_merchant_id']] = $r['rate'];
                        } else {
                            // 平台分成 < 中间人分成，平台分成全部给中间人，去掉平台分成
                            $merchant_rule[$r['receive_merchant_id']] = $nowPlatformRate;
                            unset($merchant_rule[$nowPlatformId]);
                        }
                    }
                }
                $channelRules[$mId] = $merchant_rule;
            }
        }
//        loginfo('===All Rules===', $channelRules);
        if ($merchantId === '*') {
            // 返回通用规则
            return $channelRules["*"];
        }
        if ($merchantId > 0) {
            // 返回指定商户规则，没有则返回通用规则
            return $channelRules[$merchantId] ?? $channelRules["*"];
        }
        // 返回所有规则
        return $channelRules;
    }

}
