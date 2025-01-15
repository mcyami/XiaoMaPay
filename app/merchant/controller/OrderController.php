<?php
namespace app\merchant\controller;

use app\common\controller\CrudController;
use support\Request;
use support\Response;

class OrderController extends CrudController {

    public function index(Request $request): Response {
//        $this->output = $this->_default();
        return $this->success();
    }
}