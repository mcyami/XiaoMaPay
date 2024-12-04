<?php

namespace app\admin\controller;

use app\admin\model\LogModel;
use app\admin\model\MerchantGroupModel;
use app\admin\model\PayChannelModel;
use app\admin\model\PayMethodModel;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 商户组管理
 */
class MerchantGroupController extends CrudController {

    /**
     * @var MerchantGroupModel
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
        $this->model = new MerchantGroupModel;
    }

    /**
     * 商户组列表
     * @return Response
     */
    public function index() {
        return view('merchant_group/index');
    }

    /**
     * 新增商户组
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response {
        if ($request->method() === 'POST') {
            $data = $this->insertInput($request);
            $id = $this->doInsert($data);
            $this->output['id'] = $id;
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_MERCHANT_GROUP,
                $id,
                '',
                $data
            );
            return $this->success();
        }
        return view('merchant_group/insert');
    }

    /**
     * 更新商户组
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update(Request $request): Response {
        if ($request->method() === 'GET') {
            return view('merchant_group/update');
        }
        [$id, $data] = $this->updateInput($request);
        // 获取$data中的key，作为查询的字段
        $select_field = collect($data)->keys()->toArray();
        $before_data = $this->model->select($select_field)->find($id)->toArray();
        $this->doUpdate($id, $data);
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_MERCHANT_GROUP,
            $id,
            $before_data,
            $data
        );
        return $this->success();
    }

    /**
     * 删除商户组
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
                LogModel::OP_TYPE_MERCHANT_GROUP,
                $id,
                $before_data[$id] ?? '',
                ''
            );
        }
        return $this->success();
    }

    /**
     * 商户通道配置
     * @param Request $request
     * @return Response
     */
    public function config(Request $request): Response {
        if ($request->method() === 'GET') {
            /* 通道配置界面 */
            $return = [];
            // 1- 商户组信息
            $id = $request->input('id');
            $merchantGroup = MerchantGroupModel::find($id);
            $return['channel_config'] = json_decode($merchantGroup['channel_config'], true) ?? [];

            // 2- 平台支付方式列表，每个支付方式中包含平台支付通道列表
            $return['pay_methods'] = [];
            $payMethods = PayMethodModel::where('status', PayMethodModel::PAY_METHOD_STATUS_ENABLE)
                ->select('id', 'name', 'key')
                // sort_order ASC, id ASC
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
            // 商户子通道类型选项配置
            $channel_common_items = C('MERCHANT_SUBCHANNEL_TYPE', null, []);
            foreach ($payMethods as $payMethod) {
                $channels = $channel_common_items;
                $payMethod = $payMethod->toArray();
                // 2.1- 支付通道列表 查询pay_channel表 只展示平台&启用的通道, 键为id, 值为name
                $platform_channels = PayChannelModel::where('method_id', $payMethod['id'])
                    ->where('status', PayChannelModel::PAY_CHANNEL_STATUS_ENABLE) // 启用的通道
                    ->where('mode', PayChannelModel::PAY_CHANNEL_MODE_PLATFORM) // 平台代收通道
                    ->pluck('name', 'id')
                    ->toArray();
                // 2.2-合并系统配置中的商户通道配置;
                $payMethod['channels'] = $channels + $platform_channels;
                $return['pay_methods'][] = $payMethod;
            }
            return view('merchant_group/config', $return);
        }
        return $this->success();
    }
}