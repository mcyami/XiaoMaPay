<?php

namespace app\merchant\controller;

use app\common\cache\AdminCache;
use app\common\cache\MerchantCache;
use app\common\controller\CrudController;
use app\common\model\LogModel;
use app\common\model\MerchantModel;
use app\common\utils\Auth;
use app\common\utils\Util;
use support\exception\BusinessException;
use support\Request;
use support\Response;
use Throwable;
use Webman\Captcha\CaptchaBuilder;
use Webman\Captcha\PhraseBuilder;

/**
 * 商户账户
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
     * @var MerchantModel
     */
    protected $model = null;

    /**
     * 构造函数
     */
    public function __construct() {
        $this->model = new MerchantModel;
    }

    /**
     * 登录验证码
     * @param Request $request
     * @param string $type
     * @return Response
     */
    public function captcha(Request $request, string $type = 'merchant-login'): Response {
        $builder = new PhraseBuilder(4, 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ1234567890');
        $captcha = new CaptchaBuilder(null, $builder);
        $captcha->build(120);
        $request->session()->set("captcha-$type", strtolower($captcha->getPhrase()));
        $img_content = $captcha->get();
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * 登录商户中心
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function login(Request $request): Response {
        $captcha = $request->post('captcha', '');
        // 验证码检测
        if (strtolower($captcha) !== session('captcha-merchant-login')) {
            return $this->error('error_captcha');
        }
        $request->session()->forget('captcha-merchant-login'); // 清除验证码
        // 用户名密码检测
        $username = $request->post('username', '');
        $password = $request->post('password', '');
        if (!$username) {
            return $this->error('error_username_empty');
        }
        // 检测登录限制
        $this->checkLoginLimit($username);
        $merchant = MerchantModel::where('username', $username)->first();
        if (!$merchant || !Util::passwordVerify($password, $merchant->password)) {
            return $this->error('error_username_password');
        }
        $merchant = $merchant->toArray(); // 商户存在才转换为数组，避免toArray()报错
        if ($merchant['status'] == MerchantModel::MERCHANT_STATUS_DISABLE) {
            return $this->error('error_user_disabled');
        }
        // 更新登录时间
        MerchantModel::updateLoginTime($merchant['id']);
        // 移除登录限制
        MerchantCache::clearLoginTimes($username);
        // 存储商户信息到session
        $merchant['password'] = md5($merchant['password']); // 存储MD5后的密码
        $request->session()->set('merchant', $merchant);
        $this->output = [
            'username' => $merchant['username'],
            'token' => $request->sessionId(),
        ];
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_MERCHANT,
            LogModel::OP_TYPE_MERCHANT_ACCOUNT,
            MerchantModel::merchantId()
        );
        return $this->success('success_login');
    }

    /**
     * 退出商户中心
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request): Response {
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_MERCHANT,
            LogModel::OP_TYPE_MERCHANT_ACCOUNT,
            MerchantModel::merchantId()
        );
        $request->session()->delete('merchant');
        return $this->success('success_logout');
    }

    /**
     * 账户设置
     * @return Response
     * @throws Throwable
     */
    public function index() {
        return view('account/index', ['name' => 'merchant']);
    }

    /**
     * 获取登录信息
     * @param Request $request
     * @return Response
     */
    public function info(Request $request): Response {
        $merchant_session = MerchantModel::info();
        if (!$merchant_session) {
            return $this->error('error_no_login');
        }
        $info = [
            'id' => $merchant_session['id'],
            'group_id' => $merchant_session['group_id'],
            'username' => $merchant_session['username'],
            'email' => $merchant_session['email'],
            'phone' => $merchant_session['phone'],
            'qq' => $merchant_session['qq'],
            'url' => $merchant_session['url'],
            'service' => $merchant_session['service'],
            'token' => $request->sessionId(),
        ];
        $this->output = $info;
        return $this->success();
    }

    /**
     * 更新商户个人资料
     * @param Request $request
     * @return Response
     */
    public function update(Request $request): Response {
        $allow_column = [
            'email' => 'email',
            'qq' => 'qq',
            'url' => 'url',
            'service' => 'service',
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
        $now_id = MerchantModel::merchantId(); // 当前商户ID
        $before_data = MerchantModel::find($now_id)->toArray();
        MerchantModel::updateProfile($now_id, $update_data);

        $merchant = MerchantModel::info();
        unset($update_data['password']);
        foreach ($update_data as $key => $value) {
            $merchant[$key] = $value;
        }
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_MERCHANT,
            LogModel::OP_TYPE_MERCHANT_ACCOUNT,
            $now_id,
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
        $now_id = MerchantModel::merchantId();
        $hash = MerchantModel::find($now_id)['password'];
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
        MerchantModel::updateProfile($now_id, $update_data);
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_MERCHANT,
            LogModel::OP_TYPE_MERCHANT_ACCOUNT,
            $now_id,
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
        MerchantCache::incrLoginTimes($username);
        $loginTimes = MerchantCache::getLoginTimes($username);
        if ($loginTimes > 5) {
            throw new BusinessException(trans('error_login_limit'));
        }
    }


}
