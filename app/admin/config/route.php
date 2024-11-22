<?php

use app\admin\controller\AccountController;
use app\admin\controller\DictController;
use Webman\Route;

Route::any('/admin/account/captcha/{type}', [AccountController::class, 'captcha']);

//Route::any('/admin/dict/get/{name}', [DictController::class, 'get']);