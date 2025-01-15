<?php

namespace app\merchant\controller;

use app\common\controller\CrudController;
use app\common\model\ConfigModel;
use support\Response;

/**
 * 商户配置
 */
class ConfigController extends CrudController {

    /**
     * @var ConfigModel
     */
    protected $model = null;

    /**
     * 不需要验证权限的方法
     * @var string[]
     */
    protected $noNeedAuth = ['get'];

    /**
     * 构造函数
     * @return void
     */
    public function __construct() {
        $this->model = new ConfigModel; // 配置表模型
    }

    /**
     * 获取商户中心后台配置
     * @return Response
     */
    public function get(): Response {
        $config = $this->getByDefault();
        $config['logo']['title'] = C('MERCHANT_SITE_NAME') ?? $config['logo']['title'];
        $config['logo']['image'] = C('MERCHANT_SITE_LOGO') ?? $config['logo']['image'];
        $config['logo']['footer_txt'] = C('MERCHANT_SITE_FOOTER') ?? $config['logo']['footer_txt'];
        $config['tab']['index']['title'] = C('MERCHANT_DASHBOARD_NAME') ?? $config['tab']['index']['title'];
        return json($config);
    }

    /**
     * 获取商户中心后台默认配置
     * @return array
     */
    public function getByDefault(): array {
        return [
            "logo" => [
                "title" => "Merchant Admin",
                "image" => "/admin/images/logo.png",
                "icp" => "",
                "beian" => "",
                "footer_txt" => ""
            ],
            "menu" => [
                "data" => "/merchant/menu/get",
                "accordion" => false,
                "collapse" => false,
                "control" => false,
                "controlWidth" => 2000,
                "select" => 0,
                "async" => true
            ],
            "tab" => [
                "enable" => true,
                "keepState" => true,
                "preload" => false,
                "session" => true,
                "max" => "20",
                "index" => [
                    "id" => "0",
                    "href" => "/merchant/index/dashboard",
                    "title" => "商户中心",
                ]
            ],
            "theme" => [
                "defaultColor" => "2",
                "defaultMenu" => "light-theme",
                "defaultHeader" => "light-theme",
                "allowCustom" => false,
                "banner" => true
            ],
            "colors" => [
                ["id" => "1", "color" => "#36b368", "second" => "#f0f9eb"],
                ["id" => "2", "color" => "#2d8cf0", "second" => "#ecf5ff"],
                ["id" => "3", "color" => "#f6ad55", "second" => "#fdf6ec"],
                ["id" => "4", "color" => "#f56c6c", "second" => "#fef0f0"],
                ["id" => "5", "color" => "#3963bc", "second" => "#ecf5ff"]
            ],
            "other" => ["keepLoad" => "500", "autoHead" => true, "footer" => true],
            "header" => ["message" => false]
        ];
    }

}
