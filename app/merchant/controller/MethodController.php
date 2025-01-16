<?php
namespace app\merchant\controller;

use app\common\controller\CrudController;
use app\common\model\PayMethodModel;

/**
 * 支付方式
 */
class MethodController extends CrudController {

    /**
     * @var PayMethodModel
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
        $this->model = new PayMethodModel;
    }

}