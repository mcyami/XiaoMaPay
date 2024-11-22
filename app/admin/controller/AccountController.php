<?php

namespace app\admin\controller;

use app\admin\cache\AdminCache;
use app\admin\model\AdminModel;
use app\admin\model\LogModel;
use app\common\utils\Auth;
use app\common\utils\Util;
use support\exception\BusinessException;
use support\Request;
use support\Response;
use Throwable;
use Webman\Captcha\CaptchaBuilder;
use Webman\Captcha\PhraseBuilder;

/**
 * 管理员账户
 */
class AccountController extends CrudController {
    /**
     * 不需要登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ['login', 'logout', 'captcha'];

    /**
     * 不需要鉴权的方法
     * @var string[]
     */
    protected $noNeedAuth = ['info'];

    /**
     * @var AdminModel
     */
    protected $model = null;

    /**
     * 构造函数
     */
    public function __construct() {
        $this->model = new AdminModel;
    }

    /**
     * 登录验证码
     * @param Request $request
     * @param string $type
     * @return Response
     */
    public function captcha(Request $request, string $type = 'login'): Response {
        $builder = new PhraseBuilder(4, 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ1234567890');
        $captcha = new CaptchaBuilder(null, $builder);
        $captcha->build(120);
        $request->session()->set("captcha-$type", strtolower($captcha->getPhrase()));
        $img_content = $captcha->get();
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * 登录后台
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function login(Request $request): Response {
        $this->checkDatabaseAvailable();
        $captcha = $request->post('captcha', '');
        // 验证码检测
        if (strtolower($captcha) !== session('captcha-login')) {
            return $this->error('error_captcha');
        }
        $request->session()->forget('captcha-login');
        // 用户名密码检测
        $username = $request->post('username', '');
        $password = $request->post('password', '');
        if (!$username) {
            return $this->error('error_username_empty');
        }
        // 检测登录限制
        $this->checkLoginLimit($username);
        $admin = AdminModel::getByUsername($username);
        if (!$admin || !Util::passwordVerify($password, $admin->password)) {
            return $this->error('error_username_password');
        }
        if ($admin->status != AdminModel::ADMIN_STATUS_ENABLE) {
            return $this->error('error_user_disabled');
        }
        // 更新登录时间
        AdminModel::updateLoginTime($admin);
        // 移除登录限制
        AdminCache::clearLoginTimes($username);
        // 存储用户信息到session
        $session = $request->session();
        $admin = $admin->toArray();
        $admin['password'] = md5($admin['password']);
        $session->set('admin', $admin);
        $this->output = [
            'nickname' => $admin['nickname'],
            'token' => $request->sessionId(),
        ];
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_ACCOUNT,
            AdminModel::adminId()
        );
        return $this->success('success_login');
    }

    /**
     * 退出后台
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request): Response {
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_ACCOUNT,
            AdminModel::adminId()
        );
        $request->session()->delete('admin');
        return $this->success('success_logout');
    }

    /**
     * 账户设置
     * @return Response
     * @throws Throwable
     */
    public function index() {
        return view('account/index', ['name' => 'admin']);
    }

    /**
     * 获取登录信息
     * @param Request $request
     * @return Response
     */
    public function info(Request $request): Response {
        $admin = AdminModel::admin();
        if (!$admin) {
            return $this->error('error_no_login');
        }
        $info = [
            'id' => $admin['id'],
            'username' => $admin['username'],
            'nickname' => $admin['nickname'],
            'avatar' => $admin['avatar'],
            'email' => $admin['email'],
            'mobile' => $admin['mobile'],
            'isSuperAdmin' => Auth::isSuperAdmin(),
            'token' => $request->sessionId(),
        ];
        $this->output = $info;
        return $this->success();
    }

    /**
     * 更新个人资料
     * @param Request $request
     * @return Response
     */
    public function update(Request $request): Response {
        $allow_column = [
            'nickname' => 'nickname',
            'avatar' => 'avatar',
            'email' => 'email',
            'mobile' => 'mobile',
        ];
        $data = $request->post();
        $update_data = [];
        foreach ($allow_column as $key => $column) {
            if (isset($data[$key])) {
                $update_data[$column] = $data[$key];
            }
        }
        if (isset($update_data['password'])) {
            $update_data['password'] = Util::passwordHash($update_data['password']);
        }
        $before_data = AdminModel::find(AdminModel::adminId())->toArray();
        AdminModel::updateProfile(AdminModel::adminId(), $update_data);
        $admin = AdminModel::admin();
        unset($update_data['password']);
        foreach ($update_data as $key => $value) {
            $admin[$key] = $value;
        }
        $request->session()->set('admin', $admin);
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_ACCOUNT,
            AdminModel::adminId(),
            $before_data,
            $update_data
        );
        return $this->success();
    }

    /**
     * 修改密码
     * @param Request $request
     * @return Response
     */
    public function password(Request $request): Response {
        $admin_id = AdminModel::adminId();
        $hash = AdminModel::find($admin_id)['password'];
        $password = $request->post('password');
        if (!$password) {
            return $this->error('error_password_empty');
        }
        if ($request->post('password_confirm') !== $password) {
            return $this->error('error_password_confirm');
        }
        if (!Util::passwordVerify($request->post('old_password'), $hash)) {
            return $this->error('error_old_password');
        }
        $new_password = Util::passwordHash($password);
        $update_data = [
            'password' => $new_password
        ];
        AdminModel::updateProfile($admin_id, $update_data);
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_ACCOUNT,
            AdminModel::adminId(),
            $hash, // 旧密码
            $new_password // 新密码
        );
        return $this->success();
    }

    /**
     * 检查登录频率限制
     * @param $username
     * @return void
     * @throws BusinessException
     */
    protected function checkLoginLimit($username) {
        AdminCache::incrLoginTimes($username);
        $loginTimes = AdminCache::getLoginTimes($username);
        if ($loginTimes > 5) {
            throw new BusinessException(trans('error_login_limit'));
        }
    }

    protected function checkDatabaseAvailable() {
        if (!config('database')) {
            throw new BusinessException(trans('error_database'));
        }
    }

}
