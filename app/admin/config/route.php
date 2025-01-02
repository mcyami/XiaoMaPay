<?php

use app\admin\controller\AccountController;
use Webman\Route;

Route::any('/admin/account/captcha/{type}', [AccountController::class, 'captcha']);
