<?php
namespace app\merchant\controller;

use app\common\controller\CrudController;
use app\common\model\PayChannelModel;

/**
 * 支付通道
 */
class ChannelController extends CrudController {

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

}