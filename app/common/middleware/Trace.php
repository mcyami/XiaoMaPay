<?php

namespace app\common\middleware;

use Ramsey\Uuid\Uuid;
use support\Context;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 生成请求 re_trace_id
 */
class Trace implements MiddlewareInterface {
    public function process(Request $request, callable $handler): Response {
        $re_trace_id = strval(Uuid::uuid1()->getHex());
        Context::set('re_trace_id', $re_trace_id);
        $request_info = [
            'domain' => $request->host(),
            'method' => $request->method(),
            'request_url' => $request->method() . ' ' . $request->uri(),
            'timestamp' => date('Y-m-d H:i:s'),
            'client_ip' => $request->getRealIp(),
            'request_param' => $request->all(),
        ];
//        loginfo('===req===', $request_info);
        $response = $handler($request);
        // 记录 json 返回数据
        $contentType = $response->getHeader('Content-Type');
//        if ($contentType && strpos($contentType, 'application/json') !== false) {
//            loginfo('===res===', ['contentType' => $contentType, 'response' => json_decode($response->rawBody(), true)]);
//        }
        return $response;
    }
}