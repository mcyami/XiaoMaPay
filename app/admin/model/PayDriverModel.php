<?php

namespace app\admin\model;

/**
 * 支付驱动
 * @property string $key 驱动标识(主键)
 * @property string $name 驱动显示名称
 * @property string $author 作者
 * @property string $link 链接
 * @property string $pay_types 包含的支付方式
 * @property string $trans_types 包含的转账方式
 */
class PayDriverModel extends BaseModel {
    // 表名称
    protected $table = 'xm_pay_driver';

    // 主键
    protected $primaryKey = 'key';

    // 无需自动维护时间戳
    public $timestamps = false;

}
