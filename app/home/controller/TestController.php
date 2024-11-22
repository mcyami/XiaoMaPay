<?php

namespace app\home\controller;

use app\common\entity\Test;
use app\common\model\User;
use support\Db;
use support\Redis;
use support\Request;
use Webman\App;
use Webman\RedisQueue\Redis as RedisQueue;

class TestController {
    public function index(Request $request) {
//        App::
        $default_name = 'webman';
        // 从get请求里获得name参数，如果没有传递name参数则返回$default_name
        $name = $request->get('name', $default_name);
        // 向浏览器返回字符串
//        return response('hello ' . $name);
//        return json([
//            'code' => 0,
//            'msg' => 'ok',
//            'data' => $name
//        ]);
        return view('test/index', ['name' => $name]);
    }

    public function raw(Request $request) {
//        $post = $request->rawBody();
//        $post_data = json_decode($post, true);
//        Log::info('===raw===', ['data' => $post_data, "id"=>$post_data['id'],"name"=>$post_data['name']]);

//        $id = $request->input('id');

//        $header = $request->header();
//        Log::info('===header===', [$header]);
//        return json($id);
//        var_dump($post);
//        $content_type = $header['content-type'];

//        $all = $request->all();
//        Log::info("===all===", [$all]);

//        $name = $request->input('name', 'webman');
        $only = $request->only(['id', 'name']);
//        echo $request->header('x-forwarded-proto');die();
//        $header = $request->header('x-forwarded-proto');
        $sessionId = $request->sessionId();
        $remoteIp = $request->getRemoteIp();
        $localIp = $request->getLocalIp();
        return response($localIp);

    }

    public function setHeader(Request $request) {
        // 模拟设置返回的头信息
        $response = response();
        $response->header('Content-Type', 'application/json');
        $response->withHeaders([
            'X-Header-One' => 'Header Value 1',
            'X-Header-Tow' => 'Header Value 2',
        ]);
        $response->withBody('返回的数据');
        $response->withStatus(200);
        $response->cookie('testname', 'testvalue', time() + 3600, '/');
        return $response;
    }


    public function showuser(Request $request) {
        $default_uid = 29;
        $uid = $request->get('uid', $default_uid);
        $name = Db::table('users')->where('uid', $uid)->value('username');
        return response("hello $name");
    }

    public function addUser(Request $request) {
        $user = new User;

        $user->uid = $request->input('uid');
        $user->username = $request->input('username');

        $user->save();
    }

    public function testRedis(Request $request) {
        $order_pause_key = '_order_pause_'; // 订单暂停队列
        $order_id = $request->input('order_id');
        $end_time = $request->input('end_time') ?? time() + 60 * 60 * 24;
        // 向redis的order_pause_key队列中添加当前订单，score值为过期时间
        $res = Redis::zadd($order_pause_key, $end_time, $order_id);
        return response($res);
    }

    public function setSession(Request $request) {
        $session = $request->session();
        $session->set('uid', 1);
        $session->set('username', 'mcyami');
        $session->set('lang', 'en');
        $session->set('timezone', 'Asia/Shanghai');
        return response('set session success');
    }

    public function sendQueue(Request $request) {
        // 队列名
        $queue = '_send_mail_';
        // 数据，可以直接传数组，无需序列化
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // 投递消息
        RedisQueue::send($queue, $data);
        // 投递延迟消息，消息会在60秒后处理
//        RedisQueue::send($queue, $data, 60);

        return response('redis queue test');
    }

    // 延迟队列
    public function sendQueueDelay(Request $request) {
        // 队列名
        $queue = '_send_mail_';
        // 数据，可以直接传数组，无需序列化
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // 投递延迟消息，消息会在10秒后处理
        RedisQueue::send($queue, $data, 10);

        return response('redis queue test');
    }

    public function testEntity(Request $request){
        loginfo('--当前类变量：', [Test::$_instances]);
        $uid = $request->input('uid');
        $test = Test::make($uid);
        loginfo('实例化用户：', [$test]);

        loginfo('==当前类变量：', [Test::$_instances]);
        return response('user is done');
    }
}

