<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

return [
    // 全局中间件
    '' => [
        // 跨域中间件
        app\common\middleware\Cross::class,
        // 系统配置中间件
        app\common\middleware\Config::class,
        // trace_id中间件
        app\common\middleware\Trace::class,
        // 语言中间件
        app\common\middleware\Lang::class,
    ],
    // admin 增加 AccessControl 中间件
    'admin' => [
        app\admin\middleware\AccessControl::class,
    ],
];