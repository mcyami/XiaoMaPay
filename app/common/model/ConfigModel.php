<?php

namespace app\common\model;

use app\common\cache\ConfigCache;


class ConfigModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'xm_config';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    // 时间戳存储格式
    protected $dateFormat = 'U';

    const CONFIG_STATUS_ENABLE = 1; // 启用
    const CONFIG_STATUS_DISABLE = 0; // 禁用

    public function __construct() {
        parent::__construct();
    }

    /**
     * 更新配置值
     * @param $key
     * @param $val
     * @return bool|int
     */
    public function updateVal($key, $val) {
        return $this->where('key', $key)->update(['val' => $val]);
    }

    /**
     * 获取所有配置 以key为键 以val为值
     * @param $key
     * @return mixed
     */
    public static function getConfig() {
        $data = self::where('status', self::CONFIG_STATUS_ENABLE)
            ->select('key', 'type', 'val')
            ->get();
        $configs = [];
        if ($data) {
            foreach ($data as $value) {
                $configs[$value['key']] = self::parseVal($value['type'], $value['val']);
            }
        }
        return $configs;
    }

    /**
     * 解析配置值
     * @param $type string 配置类型
     * @param $value string 配置值
     */
    public static function parseVal($type, $value) {
        switch ($type) {
            // 解析数组
            case 4:
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }

    /**
     * 解析扩展值
     * @param $extra string 扩展值
     */
    public static function parseExtra($extra) {
        if ($extra == '') return '';
        $array = preg_split('/[,;\r\n]+/', trim($extra, ",;\r\n"));
        if (is_array($array) && !empty($array)) {
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k] = $v;
            }
        } else {
            $value = $array;
        }
        return $value;
    }

    /**
     * 缓存所有配置
     * 新增、编辑、删除后需要进行缓存更新
     * @return bool
     */
    public static function cache(): bool {
        $configs = self::getConfig();
        ConfigCache::setSystemConfig($configs);
        return true;
    }

}