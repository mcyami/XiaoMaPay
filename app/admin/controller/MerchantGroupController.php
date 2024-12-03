<?php

namespace app\admin\controller;

use app\admin\model\LogModel;
use app\admin\model\MerchantGroupModel;
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
}