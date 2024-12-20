<?php

namespace app\admin\controller;

use app\admin\cache\PayDriverCache;
use app\admin\model\FeeRuleModel;
use app\admin\model\LogModel;
use app\admin\model\PayChannelModel;
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
        // 获取该通道所有的分账规则
        $rules = FeeRuleModel::where('channel_id', $id)->get();
        // 通过merchant_id分组
        $rules = $rules->groupBy('merchant_id')->toArray();
        $channelRules = [];
        // 平台默认手续费收款商户ID
        $platformMerchantId = C('PLATFORM_FEE_MERCHANT');

        /* 通过规则生成分账比例 */
        // 1、生成通道通用分账比例数组
        $cent = 100;
        $common_rule = []; // 通用规则
        $total = 0; // 规则中设置的比例总和
        $platformRate = 0; // 平台收取的比例
        if (isset($rules[0]) && !empty($rules[0])) {
            foreach ($rules[0] as $r) {
                $total = bcadd($total, $r['rate'], 2);
                $common_rule[$r['receive_merchant_id']] = $r['rate'];
            }
            // 不足100%的部分，平台收取
            if ($total < $cent) {
                $platformRate = bcsub($cent, $total, 2);
                $common_rule[$platformMerchantId] = $platformRate;
            }
        } else {
            // 通道没有设置分账规则，平台收取全部
            $common_rule[$platformMerchantId] = $cent;
        }
        $channelRules["*"] = $common_rule;

        // 2、生成指定商户分账比例，基于通用分账比例计算，中间人分账从指定平台商户ID中扣去
        if (count($rules) > 1) {
            foreach ($rules as $merchantId => $rule) {
                if ($merchantId == 0) {
                    continue;
                }
                $merchant_rule = $common_rule;
                foreach ($rule as $r) {
                    $nowPlatformId = $r['platform_id'];
                    $nowPlatformRate = $merchant_rule[$nowPlatformId] ?? 0;
                    // 指定平台商户ID在通用规则中的分成 > 当前中间人分成，则平台商户ID分成减去中间人分成，减去部分给中间人
                    if ($nowPlatformRate) {
                        if ($nowPlatformRate > $r['rate']) {
                            // 平台分成 > 中间人分成，平台分成中减掉中间人分成，加入中间人分成
                            $merchant_rule[$nowPlatformId] = bcsub($nowPlatformRate, $r['rate'], 2);
                            $merchant_rule[$r['receive_merchant_id']] = $r['rate'];
                        } else {
                            // 平台分成 < 中间人分成，平台分成全部给中间人，去掉平台分成
                            $merchant_rule[$r['receive_merchant_id']] = $nowPlatformRate;
                            unset($merchant_rule[$nowPlatformId]);
                        }
                    }
                }
                $channelRules[$merchantId] = $merchant_rule;
            }
        }
        $this->output['rules'] = $channelRules;
        return $this->success();
    }
}