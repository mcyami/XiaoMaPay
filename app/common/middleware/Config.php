<?php

namespace app\common\middleware;

use app\common\cache\ConfigCache;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 系统配置中间件
 */
class Config implements MiddlewareInterface {
    public function process(Request $request, callable $handler): Response {
        // 从Redis获取系统配置
        $config = ConfigCache::getSystemConfig();
        // 设置系统配置
        C($config);
        return $handler($request);
    }
}