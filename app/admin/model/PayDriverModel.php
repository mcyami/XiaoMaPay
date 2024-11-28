<?php

namespace app\admin\model;

use app\admin\cache\PayDriverCache;
use support\Db;

/**
 * 支付驱动
 * @property integer $id ID(主键)
 * @property string $key 驱动标识
 * @property string $name 驱动显示名称
 * @property string $author 作者
 * @property string $link 链接
 * @property string $pay_types 包含的支付方式
 * @property string $trans_types 包含的转账方式
 * @property string $inputs 配置参数形式
 * @property string $select 支持的支付形式
 * @property string $note 支付密钥填写说明
 * @property integer $bind_wxmp 是否支持绑定微信公众号
 * @property integer $bind_wxa 是否支持绑定微信小程序
 */
class PayDriverModel extends BaseModel {
    // 表名称
    protected $table = 'xm_pay_driver';

    // 无需自动维护时间戳
    public $timestamps = false;

    /**
     * 重新加载支付驱动
     */
    public static function reload() {
        // 清空缓存
        PayDriverCache::delList();
        // 清空数据表
        Db::statement("TRUNCATE TABLE xm_pay_driver");
        // 扫描驱动目录
        $driverList = self::scanDriver();
        $dataList = [];
        foreach ($driverList as $name) {
            if ($info = self::getInfo($name)) {
                if ($info['key'] != $name) continue;
                $data = [
                    'key' => $info['key'],
                    'name' => $info['name'],
                    'author' => $info['author'],
                    'link' => $info['link'],
                    'pay_types' => $info['pay_types'] ? json_encode($info['pay_types']) : '',
                    'trans_types' => $info['trans_types'] ? json_encode($info['trans_types']) : '',
                    'inputs' => $info['inputs'] ? json_encode($info['inputs']) : '',
                    'select' => $info['select'] ? json_encode($info['select']) : '',
                    'note' => $info['note'],
                    'bind_wxmp' => $info['bind_wxmp'] ? 1 : 0,
                    'bind_wxa' => $info['bind_wxa'] ? 1 : 0,
                ];
                $dataList[] = $data;
            }
        }
        // 批量插入到数据库
        if ($dataList) {
            self::insert($dataList);
        }
        // 使用支付标识作为键名
        $dataList = array_column($dataList, null, 'key');
        // 保存到缓存
        PayDriverCache::setList($dataList);
        return true;
    }

    /**
     * 扫描目录返回驱动列表
     * @return array
     */
    public static function scanDriver(): array {
        return array_map(function ($v) {
            return basename($v);
        }, glob(driver_path() . '/*', GLOB_ONLYDIR));
    }

    /**
     * 获取驱动信息
     * @param string $name
     * @return array
     */
    public static function getInfo(string $name): array {
        $filename = self::getFilename($name);
        $classname = self::getClassname($name);
        if (file_exists($filename)) {
            include_once $filename;
            if (class_exists($classname) && property_exists($classname, 'info')) {
                return $classname::$info;
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    /**
     * 获取驱动文件路径
     * @param string $name
     * @return string
     */
    public static function getFilename(string $name): string {
        return driver_path() . '/' . $name . '/' . $name . '.php';
    }

    /**
     * 获取驱动类名
     * @param string $name
     * @return string
     */
    public static function getClassname(string $name): string {
        return '\\app\\common\\driver\\' . $name . '\\' . $name;
    }

}
