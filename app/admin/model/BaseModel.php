<?php

namespace app\admin\model;

use DateTimeInterface;
use support\Model;


class BaseModel extends Model {
    /**
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * 格式化日期
     *
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i:s');
    }
}
