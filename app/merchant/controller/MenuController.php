<?php
namespace app\merchant\controller;

use app\common\controller\CrudController;
use support\Request;
use support\Response;

class MenuController extends CrudController {

    public function get(Request $request): Response {
        $this->output = $this->_default();
        return $this->success();
    }

    private function _default() {
        // 数组格式菜单
        return  [
            [
                "id" => 1,
                "title" => "商户资料",
                "icon" => "layui-icon layui-icon-set",
                "key" => "app\\merchant\\controller\\AccountController@index",
                "pid" => 0,
                "href" => "/merchant/account/index",
                "type" => 1,
                "children" => []
            ],
            [
                "id" => 11,
                "title" => "订单记录",
                "icon" => "layui-icon layui-icon-cart-simple",
                "key" => "app\\merchant\\controller\\OrderController@index",
                "pid" => 0,
                "href" => "/merchant/order/index",
                "type" => 1,
                "children" => [],
            ],
            [
                "id" => 12,
                "title" => "资金明细",
                "icon" => "layui-icon layui-icon-rmb",
                "key" => "app\\merchant\\controller\\FundController@index",
                "pid" => 0,
                "href" => "/merchant/fund/index",
                "type" => 1,
                "children" => [],
            ],
            [
                "id" => 13,
                "title" => "余额充值",
                "icon" => "layui-icon layui-icon-cellphone",
                "key" => "app\\merchant\\controller\\RechargeController@index",
                "pid" => 0,
                "href" => "/merchant/recharge/index",
                "type" => 1,
                "children" => [],
            ],
            [
                "id" => 51,
                "title" => "开发文档",
                "icon" => "layui-icon layui-icon-help",
                "key" => "developer_doc",
                "pid" => 0,
                "href" => "https://www.baidu.com",
                "type" => 1,
                "openType" => "_blank",
                "children" => []
            ],
        ];
    }
}