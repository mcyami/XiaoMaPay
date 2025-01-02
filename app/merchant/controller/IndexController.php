<?php

namespace app\merchant\controller;

use app\common\model\AdminModel;
use app\common\model\MerchantModel;
use support\Request;
use support\Response;

class IndexController {

    /**
     * 无需登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ['index'];

    /**
     * 后台主页
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response {
//        $merchant = MerchantModel::user();
        $merchant = 0;
        if (!$merchant) {
            // 登录页
            $title = C('MERCHANT_SITE_NAME', null, 'Merchant Admin');
            $logo = C('MERCHANT_SITE_LOGO', null, '/admin/images/logo.png');
            return view('account/login', ['logo' => $logo, 'title' => $title]);
        }
        return view('index/index');
    }
}