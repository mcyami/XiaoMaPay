<?php

namespace app\admin\model;


/**
 * @property integer $id (主键)
 * @property string $name 键
 * @property mixed $value 值
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class OptionModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'xm_options';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 获取配置
     * @param string $name
     * @return mixed
     */
    public static function getByName(string $name) {
        $option = OptionModel::where('name', $name)->value('value');
        $config = $option ?? '';
        return json_decode($config, true);
    }

}
