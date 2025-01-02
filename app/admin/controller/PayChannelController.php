<?php

namespace app\admin\controller;

use app\common\cache\PayDriverCache;
use app\common\model\FeeRuleModel;
use app\common\model\LogModel;
use app\common\model\MerchantModel;
use app\common\model\PayChannelModel;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 支付通道管理
 */
class PayChannelController extends CrudController {

    /**
     * @var PayChannelModel
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
        $this->model = new PayChannelModel;
    }

    /**
     * 支付通道列表
     * @return Response
     */
    public function index() {
        return view('pay_channel/index');
    }

    /**
     * 新增支付通道
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response {
        if ($request->method() === 'POST') {
            $data = $this->insertInput($request);
            $id = $this->doInsert($data);
            $this->output['id'] = $id;
            PayChannelModel::cache();
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_PAY_CHANNEL,
                $id,
                '',
                $data
            );
            return $this->success();
        }
        return view('pay_channel/insert');
    }

    /**
     * 更新支付通道
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update(Request $request): Response {
        if ($request->method() === 'GET') {
            return view('pay_channel/update');
        }
        [$id, $data] = $this->updateInput($request);
        // 获取$data中的key，作为查询的字段
        $select_field = collect($data)->keys()->toArray();
        $before_data = $this->model->select($select_field)->find($id)->toArray();
        $this->doUpdate($id, $data);
        PayChannelModel::cache();
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_PAY_CHANNEL,
            $id,
            $before_data,
            $data
        );
        return $this->success();
    }

    /**
     * 删除支付通道
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response {
        $ids = $this->deleteInput($request);
        // 历史数据 以id为key
        $before_data = $this->model->whereIn('id', $ids)->get()->keyBy('id')->toArray();
        $this->doDelete($ids);
        PayChannelModel::cache();
        foreach ($ids as $id) {
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_PAY_CHANNEL,
                $id,
                $before_data[$id] ?? '',
                ''
            );
        }
        return $this->success();
    }

    /**
     * 配置通道密钥
     * @param Request $request
     * @return Response
     */
    public function secret(Request $request): Response {
        if ($request->method() === 'GET') {
            /* 密钥配置界面 */
            $return = [];
            // 1- 通道信息
            $id = $request->input('id');
            $channel = PayChannelModel::find($id);
            $return['channel_app_type'] = $channel['app_type'] ? explode(',', $channel['app_type']) : [];
            $return['channel_secret_config'] = json_decode($channel['secret_config'], true);

            // 2- 驱动配置项
            $driverKey = $request->input('driver_key');
            // 获取驱动缓存列表
            $driverList = PayDriverCache::getList();
            $driver = $driverList[$driverKey] ?? [];

            // 支付形式列表 selects
            if (isset($driver['select']) && !empty($driver['select'])) {
                $return['selects'] = json_decode($driver['select'], true) ?? [];
            }
            $return['inputs'] = json_decode($driver['inputs'], true) ?? [];
            $return['driver_note'] = $driver['note'] ?? '';
            $return['bind_wxmp'] = $driver['bind_wxmp'];
            $return['bind_wxa'] = $driver['bind_wxa'];
            return view('pay_channel/secret', $return);
        }
        return $this->success();
    }

    /**
     * 获取通道手续费分账比例
     * @param Request $request
     * @return Response
     */
    public function getFeeRate(Request $request): Response {
        $id = $request->input('id');
        $channel = PayChannelModel::find($id);
        if ($request->method() === 'GET') {
            if (!$channel) {
                return $this->error('error_records');
            }
            $rules = FeeRuleModel::getRules($id);
            $merchantIds = [];
            foreach ($rules as $key => $rule) {
                if ($key != '*') {
                    $merchantIds[] = $key;
                }
                foreach ($rule as $k => $r) {
                    $merchantIds[] = $k;
                }
            }
            $merchantIds = array_unique($merchantIds);
            $merchants = [];
            if (!empty($merchantIds)) {
                $merchants = MerchantModel::whereIn('id', $merchantIds)->pluck('username', 'id')->toArray();
            }
            $assign = [
                'channel' => $channel,
                'rules' => $rules,
                'merchants' => $merchants,
            ];
            return view('pay_channel/fee_rate', $assign);
        }
        return $this->success();
    }
}