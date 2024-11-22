<?php

namespace app\admin\controller;

use app\admin\model\DictModel;
use app\admin\model\OptionModel;
use support\exception\BusinessException;
use support\Request;
use support\Response;
use Throwable;

/**
 * 字典管理
 */
class DictController extends BaseController {
    /**
     * 不需要授权的方法
     */
    protected $noNeedAuth = ['get'];

    /**
     * 浏览
     * @return Response
     * @throws Throwable
     */
    public function index(): Response {
        return view('dict/index');
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     */
    public function select(Request $request): Response {
        $name = $request->get('name', '');
        if ($name && is_string($name)) {
            $items = OptionModel::where('name', 'like', "dict_$name%")->get()->toArray();
        } else {
            $items = OptionModel::where('name', 'like', 'dict_%')->get()->toArray();
        }
        foreach ($items as &$item) {
            $item['name'] = DictModel::optionNameTodictName($item['name']);
        }
        $this->output = $items;
        return $this->success();
    }

    /**
     * 插入
     * @param Request $request
     * @return Response
     * @throws BusinessException|Throwable
     */
    public function insert(Request $request): Response {
        if ($request->method() === 'POST') {
            $name = $request->post('name');
            if (DictModel::get($name)) {
                return $this->error('error_record_exists');
            }
            $values = (array)$request->post('value', []);
            DictModel::save($name, $values);
        }
        return view('dict/insert');
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException|Throwable
     */
    public function update(Request $request): Response {
        if ($request->method() === 'POST') {
            $name = $request->post('name');
            if (!DictModel::get($name)) {
                return $this->error('error_records');
            }
            DictModel::save($name, $request->post('value'));
        }
        return view('dict/update');
    }

    /**
     * 删除
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response {
        $names = (array)$request->post('name');
        DictModel::delete($names);
        return $this->success();
    }

    /**
     * 获取
     * @param Request $request
     * @param $name
     * @return Response
     */
    public function get(Request $request, $name): Response {
        $this->output = (array)DictModel::get($name);
        return $this->success();
    }

}
