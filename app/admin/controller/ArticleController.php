<?php

namespace app\admin\controller;

use app\common\controller\CrudController;
use app\common\model\ArticleModel;
use app\common\model\LogModel;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 文章管理
 */
class ArticleController extends CrudController {

    /**
     * @var ArticleModel
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
        $this->model = new ArticleModel;
    }

    /**
     * 文章管理列表
     * @return Response
     */
    public function index() {
        return view('article/index');
    }

    /**
     * 新增文章
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
                LogModel::OP_TYPE_ARTICLE,
                $id,
                '',
                $data
            );
            return $this->success();
        }
        return view('article/insert');
    }

    /**
     * 更新文章
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update(Request $request): Response {
        if ($request->method() === 'GET') {
            return view('article/update');
        }
        [$id, $data] = $this->updateInput($request);
        // 获取$data中的key，作为查询的字段
        $select_field = collect($data)->keys()->toArray();
        $before_data = $this->model->select($select_field)->find($id)->toArray();
        $this->doUpdate($id, $data);
        LogModel::saveLog(
            LogModel::OP_USER_TYPE_ADMIN,
            LogModel::OP_TYPE_ARTICLE,
            $id,
            $before_data,
            $data
        );
        return $this->success();
    }

    /**
     * 删除文章
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
                LogModel::OP_TYPE_ARTICLE,
                $id,
                $before_data[$id] ?? '',
                ''
            );
        }
        return $this->success();
    }
}