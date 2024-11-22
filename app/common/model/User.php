<?php
namespace app\common\model;

use support\Model;

class User extends Model
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * 重定义主键，默认是id
     *
     * @var string
     */
//    protected $primaryKey = 'uid';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
//    public $timestamps = false;
    /**
     * 时间戳存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}