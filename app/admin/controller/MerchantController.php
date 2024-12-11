<?php

namespace app\admin\controller;

use app\admin\model\LogModel;
use app\admin\model\MerchantModel;
use app\common\utils\StringHelper;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 商户管理
 */
class MerchantController extends CrudController {

    /**
     * @var MerchantModel
     */
    protected $model = null;

    /**
     * 不需要验证权限的方法
     * @var string[]
     */
    protected $noNeedAuth = [];

    /**
     * 构造函数
     * @return void
     */
    public function __construct() {
        $this->model = new MerchantModel;
    }

    /**
     * 商户列表
     * @return Response
     */
    public function index() {
        return view('merchant/index');
    }

    /**
     * 新增商户
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response {
        if ($request->method() === 'POST') {
            $data = $this->insertInput($request);
            // 手机号掩码处理
            $data['phone'] = StringHelper::maskMobile($data['phone']);
            // 手机号加密存储
            $data['phone_encrypt'] = StringHelper::aesEncrypt($data['phone']);
            $id = $this->doInsert($data);
            $this->output['id'] = $id;
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_MERCHANT,
                $id,
                '',
                $data
            );
            return $this->success();
        }
        return view('merchant/insert');
    }

    /**
     * 更新商户
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update(Request $request): Response {
        if ($request->method() === 'GET') {
            return view('merchant/update');
        }
        [$id, $data] = $this->updateInput($request);
        if (isset($data['phone']) && !empty($data['phone'])) {
            $phone_raw = $data['phone'];
            // 手机号掩码处理
            $data['phone'] = StringHelper::maskMobile($phone_raw);
            // 手机号加密存储
            $data['phone_encrypt'] = StringHelper::aesEncrypt($phone_raw);
        }
        // 获取$data中的key，作为查询的字段
        $select_field = collect($data)->keys()->toArray();
        $before_data = $this->model->select($select_field)->find($id)->toArray();
        $this->doUpdate($id, $data);
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_MERCHANT,
            $id,
            $before_data,
            $data
        );
        return $this->success();
    }

    /**
     * 删除商户
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response {
        $ids = $this->deleteInput($request);
        // 历史数据 以id为key
        $before_data = $this->model->whereIn('id', $ids)->get()->keyBy('id')->toArray();
        $this->doDelete($ids);
        foreach ($ids as $id) {
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_MERCHANT,
                $id,
                $before_data[$id] ?? '',
                ''
            );
        }
        return $this->success();
    }

    /**
     * 商户余额变更
     * @param Request $request
     * @return Response
     */
    public function balance(Request $request): Response {
        if ($request->method() === 'GET') {
            return view('merchant/balance');
        }
        $merchant_id = $request->input('merchant_id');
        $type = $request->input('type');
        $amount = $request->input('amount') ?? 0;
        $trade_no = $request->input('trade_no') ?? '';
        $note = $request->input('note') ?? '';
        if (empty($merchant_id) || empty($type) || empty($amount)) {
            return $this->error('error_lack_param');
        }
        $result = MerchantModel::changeBalance($merchant_id, $type, $amount, $trade_no, $note);
        if ($result) {
            return $this->success();
        } else {
            return $this->error('error');
        }
    }

    /**
     * 商户通道费率
     * @param Request $request
     * @return Response
     */
    public function channelRate(Request $request): Response {
        $merchant_id = $request->input('merchant_id') ?? 0;
        // $method_key & channel_id 都不存在时，返回所有通道的费率
        $channel_id = $request->input('channel_id') ?? 0;
        $method_key = $request->input('method_key') ?? '';

        // 商户信息
        $merchant = MerchantModel::find($merchant_id);
        if (!$merchant) {
            return $this->error('error_data');
        }
        $this->output = MerchantModel::getChannelRate($merchant_id, $method_key, $channel_id);
        return $this->success();
    }
}