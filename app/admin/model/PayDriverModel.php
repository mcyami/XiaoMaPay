<?php

namespace app\admin\model;

use support\Db;

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

    /**
     * 重新加载支付驱动
     */
    public static function reload() {
        // 清空数据表
//        Db::statement("TRUNCATE TABLE xm_pay_driver");
        // 扫描驱动目录
        $driverList = self::scanDriver();

        loginfo('driverList', [$driverList]);
//        return true;
        // 保存到数据库
//        foreach ($driverList as $name) {
//            if ($config = self::getConfig($name)) {
//                if ($config['name'] != $name) continue;
//                $DB->insert('plugin', ['name' => $config['name'], 'showname' => $config['showname'], 'author' => $config['author'], 'link' => $config['link'], 'types' => implode(',', $config['types']), 'transtypes' => $config['transtypes'] ? implode(',', $config['transtypes']) : null]);
//            }
//        }
//        return true;
    }

    /**
     * 扫描目录返回驱动列表
     * @return array
     */
    public static function scanDriver(){
        // 扫码驱动目录：app/common/driver
        $dir = driver_path();
        // glob 方式获取驱动列表
        $dirArray = glob($dir . '/*', GLOB_ONLYDIR);
        return array_map(function ($v) {
            return basename($v);
        }, $dirArray);
    }

}
