<?php

namespace app\common\entity;

/**
 * 实体基类
 * Class base
 * @package app\common\entity
 * @property array $_doc
 * @property array $_modify
 */
class Base {
    // 实体数据
    protected $_doc;
    // 修改数据
    protected $_modify;

    /**
     * 构造函数
     * @param array $doc
     */
    public function __construct($doc = []) {
        $this->_doc = $doc;
    }

    /**
     * 魔术方法，执行get开头方法或获取属性
     * 使用方法：实体对象->属性名 或 实体对象->方法名
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], []);
        }
        if (isset($this->_doc[$name])) {
            return $this->_doc[$name];
        }
        return null;
    }

    /**
     * 魔术方法，执行set开头方法或设置属性
     * 使用方法：实体对象->属性名 = 值 或 实体对象->方法名 = 参数
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $method = 'set' . ucfirst($name);
        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], [$value]);
        } else {
            $this->set($name, $value);
        }
    }

    /**
     * 设置属性值
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value) {
        $this->_modify[$name] = $value;
        $this->_doc[$name] = $value;
    }

    /**
     * 保存修改
     */
    public function save() {
        $this->_modify = [];
    }

    /**
     * 魔术方法，直接返回实体数组
     * @return array
     */
    public function __toArray() {
        return $this->_doc;
    }

    /**
     * 返回实体数组
     * @return array
     */
    public function toArray() {
        return $this->_doc;
    }

    /**
     * 是否有修改
     * @return bool
     */
    public function changed() {
        return !empty($this->_modify);
    }
}