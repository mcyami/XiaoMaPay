<?php
use support\Request;
use Webman\Route;

// 加载admin应用下的路由配置
require_once app_path('admin/config/route.php');
// 加载user商户应用下的路由配置
require_once app_path('merchant/config/route.php');
// 加载api应用下的路由配置
//require_once app_path('api/config/route.php');
// 加载home应用下的路由配置
//require_once app_path('home/config/route.php');

Route::fallback(function (Request $request) {
    return response($request->uri() . ' not found' , 404);
});

//Route::disableDefaultRoute();