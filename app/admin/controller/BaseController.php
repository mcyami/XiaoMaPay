<?php

namespace app\admin\controller;

use support\Response;

/**
 * 基础控制器
 */
class BaseController {

    /**
     * 服务类
     */
    protected $service = null;

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

    protected $count = null;

    protected $extra = null;

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

    /**
     * 成功返回
     * @param string $msg
     * @return Response
     */
    protected function success(string $msg = 'success'): Response {
        $this->output = $this->output ?? [];
        $msg = trans($msg);
        $return = ['code' => 0, 'msg' => $msg, 'data' => $this->output, 'count' => $this->count, 'extra' => $this->extra];
        if ($this->count === null) {
            unset($return['count']);
        }
        if ($this->extra === null) {
            unset($return['extra']);
        }
//        loginfo('===success===', $return);
        return json($return);
    }

    /**
     * 失败返回
     * @param string $msg
     * @return Response
     */
    protected function error(string $msg = 'error'): Response {
        $this->output = $this->output ?? [];
        $msg = trans($msg);
        $return = ['code' => 1, 'msg' => $msg, 'data' => $this->output];
        loginfo('===error===', $return);
        return json($return);
    }

}
