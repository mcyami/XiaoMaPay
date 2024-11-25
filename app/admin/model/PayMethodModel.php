<?php

namespace app\admin\model;

/**
 * 支付方式
 * @property integer $id ID(主键)
 * @property string $name 支付方式显示名称
 * @property string $driver_key 驱动标识
 * @property integer $is_pc 是否支持PC 1是 0否
 * @property integer $is_mobile 是否支持移动端 1是 0否
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 * @property integer $sort 排序
 * @property integer $status 状态 1正常 0禁用
 */
class PayMethodModel extends BaseModel {
    // 表名称
    protected $table = 'xm_pay_method';

    // 主键
    protected $primaryKey = 'id';

    // 时间戳存储格式
    protected $dateFormat = 'U';

    const PAY_METHOD_STATUS_ENABLE = 1; // 启用
    const PAY_METHOD_STATUS_DISABLE = 0; // 禁用

}