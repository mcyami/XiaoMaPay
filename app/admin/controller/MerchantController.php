<?php

namespace app\admin\controller;

use app\admin\cache\PayChannelCache;
use app\admin\cache\PayMethodCache;
use app\admin\model\LogModel;
use app\admin\model\MerchantGroupModel;
use app\admin\model\MerchantModel;
use app\admin\model\PayChannelModel;
use app\admin\model\PayMethodModel;
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
     * 商户后台提单
     * @param Request $request
     * @return Response
     */
    public function addOrder(Request $request): Response {
        if ($request->method() === 'GET') {
            return view('merchant/add_order');
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
        // 商户组信息
        $group = MerchantGroupModel::find($merchant->group_id);
        if (!$group) {
            return $this->error('error_data');
        }
        // 商户组通道费率
        $group_channel_config = json_decode($group->channel_config, true) ?? [];

        // 支付方式列表
        $payMethods = PayMethodCache::getList();

        // 平台通道列表
        $payChannels = PayChannelCache::getList();

        $channelRateList = [];
        foreach ($payMethods as $payMethod) {
            // 去除禁用的支付方式
            if($payMethod['status'] != PayMethodModel::PAY_METHOD_STATUS_ENABLE) {
                continue;
            }
            $item = [
                'enabled' => 1, // 开启状态
                'channels' => [], // 平台通道列表
                'sub_channels' => [], // 商户通道列表
            ];
            // 商户组是否开启该支付方式 0:关闭 -1:随机平台通道 -2:随机商户通道 >0:指定平台通道
            if(!isset($group_channel_config[$payMethod['id']])) {
                // 没有配置改支付方式
                $item['enabled'] = 0;
                continue;
            }
            $channel_config = $group_channel_config[$payMethod['id']] ?? [];
            $channel_config_type = $channel_config['type'] ?? 0;
            $channel_config_rate = $channel_config['rate'] ?? 0; // 不存在则查询通道默认费率
            if ($channel_config_type == 0) {
                // 配置关闭通道
                $item['enabled'] = 0;
                continue;
            }

            // 随机平台通道，将平台通道列表全部加入到 channels
            if($channel_config_type == -1){
                // 查询平台通道列表
                foreach ($payChannels as $k => $channelInfo) {
                    if(
                        $channelInfo['method_id'] != $payMethod['id']
                        || $channelInfo['mode'] != PayChannelModel::PAY_CHANNEL_MODE_PLATFORM
                        || $channelInfo['status'] != PayChannelModel::PAY_CHANNEL_STATUS_ENABLE
                    ) {
                        continue;
                    }
                    if($channel_config_rate == 0) {
                        $channel_config_rate = $channelInfo['ratio'];
                    }
                    array_push($item['channels'], [$k=>$channel_config_rate]);
                }
            }

            // 随机商户通道，将商户通道列表全部加入到 sub_channels TODO
//            if($channel_config_type == -2){
//
//            }

            // 指定平台通道加入到 channels
            if($channel_config_type > 0){
                // $channel_config_rate 不存在则查询通道默认费率
                if($channel_config_rate == 0) {
                    // 查询通道默认费率 只获取ratio字段
                    $channel_config_rate = PayChannelModel::where('id', $channel_config_type)->pluck('ratio');
                }
                array_push($item['channels'], [$channel_config_type=>$channel_config_rate]);
            }
            $channelRateList[$payMethod['key']] = $item;
        }

        if($method_key) {
            if($channel_id) {
                $return = $channelRateList[$method_key]['channels'][$channel_id] ?? 0;
            } else {
                $return[$method_key] = $channelRateList[$method_key];
            }
        } else {
            $return = $channelRateList;
        }

        $this->output = $return;
        return $this->success();
    }
}