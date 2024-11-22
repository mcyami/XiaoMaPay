<?php

namespace app\common\model;

use support\Model;

/**
 * 模型层示例
 */
class TestModel extends Model {
    // 表字段的枚举值 全部使用模型常量定义
    const TEST_STATUS_ON = 1; // 开启
    const TEST_STATUS_OFF = 0; // 关闭

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'test';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 时间戳存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}