<?php

namespace app\admin\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

/**
 * Class Handler
 * @package support\exception
 */
class TinywanHandler extends \Tinywan\ExceptionHandler\Handler {
    public function render(Request $request, Throwable $exception): Response {
        $this->config = array_merge($this->config, config('plugin.tinywan.exception-handler.app.exception_handler', []) ?? []);
        // 返回请求参数信息 prod环境不返回
        if(getenv('APP_ENV') !== 'prod') {
            $this->addRequestInfoToResponse($request);
        }
        $this->solveAllException($exception);
        $this->addDebugInfoToResponse($exception);
        $this->triggerNotifyEvent($exception);
        $this->triggerTraceEvent($exception);
        // 记录异常日志
        loginfo('===exception===', [$this->statusCode, $this->errorMessage, $this->responseData]);
        return $this->buildResponse();
    }

}
