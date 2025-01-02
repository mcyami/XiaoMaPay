<?php

use app\common\cache\ConfigCache;
use support\Context;
use support\Log;

/**
 * 带trace_id 的日志，方便追踪同一个请求中的日志
 * @param string $message
 * @param array $context
 * @return bool
 */
function loginfo($message = '', $context = []): bool {
    $message = Context::get('re_trace_id') . ' ' . $message;
    Log::info($message, $context);
    return true;
}

/**
 * 获取和设置配置参数 支持批量定义 【$_config 静态变量常驻内存】
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function C($name = null, $value = null, $default = null) {
    $_config = Context::get('system_config') ?: [];
    if (empty ($_config)) {
        // 为空时自动获取配置信息，兼容RedisQueue消费者模式的调用
        $_config = array_change_key_case(ConfigCache::getSystemConfig(), CASE_UPPER);
        Context::set('system_config', $_config);
    }
    // 无参数时获取所有
    if (empty ($name)) {
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtoupper($name);
            if (is_null($value)) {
                return isset ($_config [$name]) ? $_config [$name] : $default;
            }
            $_config [$name] = $value;
            return null;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        $name [0] = strtoupper($name [0]);
        if (is_null($value)) {
            return isset ($_config [$name [0]] [$name [1]]) ? $_config [$name [0]] [$name [1]] : $default;
        }
        $_config [$name [0]] [$name [1]] = $value;
        return null;
    }
    // 批量设置
    if (is_array($name)) {
        Context::set('system_config', array_merge($_config, array_change_key_case($name, CASE_UPPER)));
        return null;
    }
    return null; // 避免非法参数
}

/**
 * 获取驱动目录
 * @return string
 */
function driver_path() {
    return app_path() . '/common/driver';
}