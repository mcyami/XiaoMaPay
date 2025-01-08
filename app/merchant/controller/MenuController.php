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
                "title" => "基本信息",
                "icon" => "layui-icon layui-icon-set",
                "key" => "merchant",
                "pid" => 0,
                "href" => "",
                "type" => 0,
                "children" => [
                    [
                        "id" => 2,
                        "title" => "商户资料",
                        "icon" => "",
                        "key" => "app\\merchant\\controller\\AccountController@index",
                        "pid" => 1,
                        "href" => "/merchant/account/index",
                        "type" => 1,
                        "children" => [],
                    ],

                ],
            ],
        ];
    }
}