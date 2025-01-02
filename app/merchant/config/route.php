<?php

use app\merchant\controller\AccountController;
use Webman\Route;

Route::any('/merchant/account/captcha/{type}', [AccountController::class, 'captcha']);
