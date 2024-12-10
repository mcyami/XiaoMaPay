<?php

namespace app\admin\controller;

use app\admin\model\LogModel;
use app\admin\model\OrderModel;
use app\common\utils\StringHelper;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 订单管理
 */
class OrderController extends CrudController {

    /**
     * @var OrderModel
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
        $this->model = new OrderModel;
    }

    /**
     * 订单列表
     * @return Response
     */
    public function index() {
        return view('order/index');
    }

    /**
     * 通用格式化
     * @param $items
     * @param $total
     * @return Response
     */
    protected function formatNormal($items, $total): Response {
        foreach ($items as &$item) {
            $item['pay_at'] = $item['pay_at'] ? date('Y-m-d H:i:s', $item['pay_at']) : '';
            $item['refund_at'] = $item['refund_at'] ? date('Y-m-d H:i:s', $item['refund_at']) : '';
        }
        $this->output = $items;
        $this->count = $total;
        return $this->success();
    }

    /**
     * 后台提单
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
                LogModel::OP_TYPE_ORDER,
                $id,
                '',
                $data
            );
            return $this->success();
        }
        return view('order/insert');
    }

    /**
     * 更新订单
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update(Request $request): Response {
        if ($request->method() === 'GET') {
            return view('order/update');
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
            LogModel::OP_TYPE_ORDER,
            $id,
            $before_data,
            $data
        );
        return $this->success();
    }

    /**
     * 删除订单
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function delete(Request $request): Response {
        $ids = $this->deleteInput($request);
        // 历史数据 以id为key
        $before_data = $this->model->whereIn('id', $ids)->get()->keyBy('id')->toArray();
        $this->doDelete($ids);
        foreach ($ids as $id) {
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_ORDER,
                $id,
                $before_data[$id] ?? '',
                ''
            );
        }
        return $this->success();
    }
}