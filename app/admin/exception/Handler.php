<?php

namespace app\admin\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

/**
 * Class Handler
 * @package support\exception
 */
class Handler extends \support\exception\Handler {
    public function render(Request $request, Throwable $exception): Response {
        $code = $exception->getCode();
        $debug = $this->_debug ?? $this->debug;
        // 记录异常日志
        loginfo('===exception===', ['json' => $request->expectsJson(), 'code' => $code, 'debug' => $debug, 'msg' => $exception->getMessage()]);
        if ($request->expectsJson()) {
            $json = ['code' => $code ?: 500, 'msg' => $debug ? $exception->getMessage() : 'Server internal error', 'type' => 'failed'];
            $debug && $json['traces'] = (string)$exception;
            return new Response(200, ['Content-Type' => 'application/json'],
                \json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        $error = $debug ? \nl2br((string)$exception) : 'Server internal error';
        return new Response(500, [], $error);
    }
}
