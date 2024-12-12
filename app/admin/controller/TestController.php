<?php

namespace app\admin\controller;

use app\admin\model\MerchantModel;
use app\admin\model\OrderModel;
use app\common\utils\StringHelper;
use support\Cache;
use support\Db;

class TestController {
    public function test() {
//        return 2 / 0;
//        return Cache::set('test', 'test123');
//        return config();
        C('TEMP_VAL', 'abc');
        return json(C('TEMP_VAL'));
//        return boolval(getenv(getenv('APP_ENV') . '_APP_DEBUG')) ;
//        return config('app.debug');
//        return json([
//            'status' => config('app.debug'),
//            'message' => 'Hello, World!'
//        ]);

    }

    public function setCache(){
        Cache::setMultiple(['test' => 'test123', 'test2' => 'test123']);
        return true;
    }

    public function getCache(){
        config();
        return json([
            'status' => 'success',
            'message' => Cache::get('test')
        ]);
    }

    public function getSql(){
        $sql = 'select * from wa_admins where id = 1';
        // 执行原生sql查询
        $result = Db::table('wa_admins')->where('id', 1)->get();

        return json([
            'status' => 'success',
            'message' => $result
        ]);
    }

    public function aesEncrypt() {
        $value = '13588888888';

        return json([
            'status' => 'success',
            'message' => StringHelper::aesEncrypt($value)
        ]);
    }

    public function aesDecrypt() {
        $value = 'V66kR0S7RYYgzUfG5SVvgQ==';

        return json([
            'status' => 'success',
            'message' => StringHelper::aesDecrypt($value)
        ]);
    }

    public function sendQueue() {
        $order_id = 1232333;
        OrderModel::sendFundQueue($order_id);
        return json([
            'status' => 'success',
            'message' => 'send queue success'
        ]);
    }

    public function getTradeNo() {
        return json([
            'status' => 'success',
            'message' => OrderModel::getTradeNo()
        ]);
    }

    public function getMerchant(){
        $merchant_id = '100007';
        $merchant = MerchantModel::find($merchant_id);
        loginfo('merchant', $merchant->toArray());
        return json([
            'status' => 'success',
            'message' => $merchant->toArray()
        ]);
    }
}