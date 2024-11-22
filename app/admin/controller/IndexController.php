<?php

namespace app\admin\controller;

use app\admin\cache\ConfigCache;
use app\admin\cache\RoleCache;
use app\admin\cache\RuleCache;
use app\admin\model\AdminModel;
use app\admin\model\ConfigModel;
use app\admin\model\RoleModel;
use app\admin\model\RuleModel;
use app\admin\model\UserModel;
use app\common\utils\Util;
use support\Request;
use support\Response;
use Throwable;
use Workerman\Worker;

class IndexController {

    /**
     * 无需登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ['index'];

    /**
     * 不需要鉴权的方法
     * @var string[]
     */
    protected $noNeedAuth = ['dashboard'];

    /**
     * 后台主页
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response {
        $admin = AdminModel::admin();
        if (!$admin) {
            $title = C('SYS_SITE_NAME', null, 'web admin');
            $logo = C('SYS_SITE_LOGO', null, '/admin/images/logo.png');
            return view('account/login', ['logo' => $logo, 'title' => $title]);
        }
        return view('index/index');
    }

    /**
     * 检查缓存，自动生成缓存(主要用于首次部署，或服务器转移、重启导致的缓存全部丢失的情况)
     * @return bool
     */
    private function checkCache(): bool {
        // 系统配置缓存
        if(!ConfigCache::getSystemConfig()) {
            ConfigModel::cache();
        }
        // 角色缓存
        if(!RoleCache::getSystemRole() || !RoleCache::getSystemRoleRaw()) {
            RoleModel::cache();
        }
        // 权限缓存
        if (!RuleCache::getSystemRule() || !RuleCache::getSystemRuleRaw()) {
            RuleModel::cache();
        }
        return true;
    }

    /**
     * 仪表板
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function dashboard(Request $request): Response {
        // 缓存检测，如果缓存不存在则生成
        $this->checkCache();

        // 今日新增用户数
        $today_user_count = UserModel::where('created_at', '>', date('Y-m-d') . ' 00:00:00')->count();
        // 7天内新增用户数
        $day7_user_count = UserModel::where('created_at', '>', date('Y-m-d H:i:s', time() - 7 * 24 * 60 * 60))->count();
        // 30天内新增用户数
        $day30_user_count = UserModel::where('created_at', '>', date('Y-m-d H:i:s', time() - 30 * 24 * 60 * 60))->count();
        // 总用户数
        $user_count = UserModel::count();
        // mysql版本
        $version = Util::db()->select('select VERSION() as version');
        $mysql_version = $version[0]->version ?? 'unknown';

        $day7_detail = [];
        $now = time();
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', $now - 24 * 60 * 60 * $i);
            $day7_detail[substr($date, 5)] = UserModel::where('created_at', '>', "$date 00:00:00")
                ->where('created_at', '<', "$date 23:59:59")->count();
        }

        return raw_view('index/dashboard', [
            'today_user_count' => $today_user_count,
            'day7_user_count' => $day7_user_count,
            'day30_user_count' => $day30_user_count,
            'user_count' => $user_count,
            'php_version' => PHP_VERSION,
            'workerman_version' => Worker::VERSION,
            'webman_version' => Util::getPackageVersion('workerman/webman-framework'),
            'admin_version' => config('plugin.admin.app.version'),
            'mysql_version' => $mysql_version,
            'os' => PHP_OS,
            'day7_detail' => array_reverse($day7_detail),
        ]);
    }

}
