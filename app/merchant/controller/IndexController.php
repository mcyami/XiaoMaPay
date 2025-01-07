<?php

namespace app\merchant\controller;

use app\common\model\AdminModel;
use app\common\model\MerchantModel;
use app\common\utils\Util;
use support\Request;
use support\Response;
use Workerman\Worker;

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
        $merchant = MerchantModel::info();
        if (!$merchant) {
            // 登录页
            $title = C('MERCHANT_SITE_NAME', null, 'Merchant Admin');
            $logo = C('MERCHANT_SITE_LOGO', null, '/admin/images/logo.png');
            return view('account/login', ['logo' => $logo, 'title' => $title]);
        }
        return view('index/index');
    }

    /**
     * 仪表板
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function dashboard(Request $request): Response {
        // 今日新增用户数
//        $today_user_count = UserModel::where('created_at', '>', date('Y-m-d') . ' 00:00:00')->count();
        $today_user_count = 0;
        // 7天内新增用户数
//        $day7_user_count = UserModel::where('created_at', '>', date('Y-m-d H:i:s', time() - 7 * 24 * 60 * 60))->count();
        $day7_user_count = 0;

        // 30天内新增用户数
//        $day30_user_count = UserModel::where('created_at', '>', date('Y-m-d H:i:s', time() - 30 * 24 * 60 * 60))->count();
        $day30_user_count = 0;
        // 总用户数
//        $user_count = UserModel::count();
        $user_count = 0;

        // mysql版本
        $version = Util::db()->select('select VERSION() as version');
        $mysql_version = $version[0]->version ?? 'unknown';

        $day7_detail = [];
        $now = time();
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', $now - 24 * 60 * 60 * $i);
//            $day7_detail[substr($date, 5)] = UserModel::where('created_at', '>', "$date 00:00:00")
//                ->where('created_at', '<', "$date 23:59:59")->count();
            $day7_detail[substr($date, 5)] = 0;
        }

        return raw_view('index/dashboard', [
            'today_user_count' => $today_user_count,
            'day7_user_count' => $day7_user_count,
            'day30_user_count' => $day30_user_count,
            'user_count' => $user_count,
            'php_version' => PHP_VERSION,
            'workerman_version' => Worker::VERSION,
            'webman_version' => Util::getPackageVersion('workerman/webman-framework'),
            'admin_version' => getenv('APP_VERSION'),
            'mysql_version' => $mysql_version,
            'os' => PHP_OS,
            'day7_detail' => array_reverse($day7_detail),
        ]);
    }

}