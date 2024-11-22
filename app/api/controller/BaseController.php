<?php

namespace app\api\controller;

use Ramsey\Uuid\Uuid;
use support\Context;
use support\Log;
use support\Model;
use support\Request;
use support\Response;

/**
 * 基础控制器
 */
class BaseController {

    /**
     * @var Model
     */
    protected $model = null;

    /**
     * 无需登录及鉴权的方法
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 需要登录无需鉴权的方法
     * @var array
     */
    protected $noNeedAuth = [];

    /**
     * 数据限制
     * null 不做限制，任何管理员都可以查看该表的所有数据
     * auth 管理员能看到自己以及自己的子管理员插入的数据
     * personal 管理员只能看到自己插入的数据
     * @var string
     */
    protected $dataLimit = null;

    /**
     * 数据限制字段
     */
    protected $dataLimitField = 'admin_id';

    protected $output = [];

    /**
     * 请求日志信息
     * @return array
     */
    protected function requestInfo(): array {
        $request = request();
        return [
            'domain' => $request->host(),
            'method' => $request->method(),
            'request_url' => $request->method() . ' ' . $request->uri(),
            'timestamp' => date('Y-m-d H:i:s'),
            'client_ip' => $request->getRealIp(),
            'request_param' => $request->all(),
        ];
    }

    /**
     * 返回格式化json数据
     *
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return Response
     */
    protected function json(int $code, string $msg = 'ok', array $data = []): Response {
        $response = json(['code' => $code, 'data' => $data, 'msg' => $msg]);
        return $response;
    }

    protected function success(int $code = 0, string $msg = 'success'): Response {
        $this->output = $this->output ?? [];
        loginfo('===success===', [$code, $msg, $this->output]);
        return $this->json($code, $msg, $this->output);
    }

    protected function fail(int $code = 1, string $msg = 'error'): Response {
        $this->output = $this->output ?? [];
        loginfo('===error===', [$code, $msg, $this->output]);
        return $this->json($code, $msg, $this->output);
    }

}
