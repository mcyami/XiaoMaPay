<?php

namespace app\common\entity;
use app\common\cache\TestCache;
use app\common\model\TestModel;

/**
 * 实体层示例
 */
class Test extends Base {
    // 实体对象
    public static $_instances = [];

    public function __construct($doc = []) {
        parent::__construct($doc);
    }

    const TEST_STATUS_ON = TestModel::TEST_STATUS_ON; // 开启

    const TEST_STATUS_OFF = TestModel::TEST_STATUS_OFF; // 关闭

    /**
     * 获取实体对象
     * @param $test_id
     * @return Test
     */
    public static function make($test_id) {
        $test_user = [
            1 => [
                'id' => 1,
                'name' => '张三',
                'age' => 18,
            ],
            2 => [
                'id' => 2,
                'name' => '李四',
                'age' => 20,
            ],
            3 => [
                'id' => 3,
                'name' => '王五',
                'age' => 22,
            ],
            4 => [
                'id' => 4,
                'name' => '赵六',
                'age' => 24,
            ]
        ];
        if (empty(self::$_instances[$test_id])) {
            // 从缓存中获取实体数据
//            $doc = TestCache::getProfile($test_id);
            $doc = $test_user[$test_id];
            if (empty($doc)) {
                // 从数据库中获取实体数据
                $doc = TestModel::where(['id' => $test_id])->first();
                if ($doc) {
                    $doc = $doc->toArray();
                    // 将实体数据保存到缓存中
                    TestCache::setProfile($test_id, $doc);
                }
            }
            self::$_instances[$test_id] = new self($doc);
        }
        return self::$_instances[$test_id];
    }

    /**
     * 保存实体对象
     */
    public function save() {
        TestCache::setProfile($this->id, $this->_doc);
        $model = TestModel::where(['id' => $this->id])->first();
        if (empty($model)) {
            $model = new TestModel();
        }
        foreach ($this->_modify as $key => $value) {
            $model->$key = $value;
        }
        $model->save();
    }
}