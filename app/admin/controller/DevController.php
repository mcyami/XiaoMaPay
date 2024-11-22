<?php

namespace app\admin\controller;

use support\Response;
use Throwable;

/**
 * 开发辅助相关
 */
class DevController {
    /**
     * 表单构建
     * @return Response
     * @throws Throwable
     */
    public function formBuild() {
        return view('dev/form-build');
    }

}
