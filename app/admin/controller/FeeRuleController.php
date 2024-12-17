<?php

namespace app\admin\controller;

use app\admin\model\FeeRuleModel;
use app\admin\model\LogModel;
use app\admin\model\OrderModel;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 订单手续费分账规则管理
 */
class FeeRuleController extends CrudController {

    /**
     * @var FeeRuleModel
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
        $this->model = new FeeRuleModel;
    }

    /**
     * 分账规则列表
     * @return Response
     */
    public function index() {
        return view('fee_rule/index');
    }

    /**
     * 新增分账规则
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response {
        if ($request->method() === 'POST') {
            $data = $this->insertInput($request);
            $id = $this->doInsert($data);
            $this->model->cache();
            $this->output['id'] = $id;
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_FEE_RULE,
                $id,
                '',
                $data
            );
            return $this->success();
        }
        return view('fee_rule/insert');
    }

    /**
     * 更新分账规则
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update(Request $request): Response {
        if ($request->method() === 'GET') {
            return view('fee_rule/update');
        }
        [$id, $data] = $this->updateInput($request);
        // 获取$data中的key，作为查询的字段
        $select_field = collect($data)->keys()->toArray();
        $before_data = $this->model->select($select_field)->find($id)->toArray();
        $this->doUpdate($id, $data);
        $this->model->cache();
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_FEE_RULE,
            $id,
            $before_data,
            $data
        );
        return $this->success();
    }

    /**
     * 删除分账规则
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function delete(Request $request): Response {
        $ids = $this->deleteInput($request);
        // 历史数据 以id为key
        $before_data = $this->model->whereIn('id', $ids)->get()->keyBy('id')->toArray();
        $this->doDelete($ids);
        $this->model->cache();
        foreach ($ids as $id) {
            LogModel::saveLog(
                LogModel::OP_USER_TYPE_ADMIN,
                LogModel::OP_TYPE_FEE_RULE,
                $id,
                $before_data[$id] ?? '',
                ''
            );
        }
        return $this->success();
    }
}