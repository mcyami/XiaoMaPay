<?php

namespace app\api\controller;

use app\api\exception\ApiBusinessException;
use app\api\exception\MyBusinessException;
use support\exception\BusinessException;
use support\Log;
use support\Request;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;
use Tinywan\ExceptionHandler\Exception\RouteNotFoundException;
use Tinywan\Jwt\JwtToken;

class IndexController extends BaseController {
    public function index(Request $request) {

        return 'api index';
    }

    /**
     * 获取Token
     */
    public function getToken(Request $request) {
        /* 用户信息 */
        $user = [
            'id' => 2022, // 这里必须是一个全局抽象唯一id
            'name' => 'mcyami',
            'email' => 'mcyami@163.com'
        ];
        $token = JwtToken::generateToken($user);
        $this->output= $token;
        return $this->success(200, '获取Token成功');
    }

    /**
     * 解析Token
     */
    public function parseToken(Request $request) {
        $uid = JwtToken::getCurrentId();
        $all = JwtToken::getExtend();
        $this->output['uid'] = $uid;
        $this->output['all'] = $all;
        return $this->success(200, 'ok');
    }

    /**
     * 抛出自定义业务异常
     */
    public function testThrow(){
        throw new BadRequestHttpException('自定义业务异常');
    }

    /**
     * 测试多语言
     */
    public function testLang(Request $request) {
        return $this->success(200, trans('home_page'));
    }
}