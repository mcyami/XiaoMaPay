<?php

namespace app\common\middleware;

use app\common\utils\StringHelper;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 多语言中间件
 */
class Lang implements MiddlewareInterface {

    public function process(Request $request, callable $handler): Response {
        $language = 'zh_CN'; // 默认语言
        // 1、lang 参数优先级最高
        $lang = $request->header('lang', '');
        // 2、session 语言
        if($request->input('lang')) {
            // url方式设置语言
            $request->session()->set('lang', $request->input('lang'));
        }
        $sessionLang = session('lang', '');
        // 3、cookie 语言
        $cookieLang = StringHelper::getLangFromAcceptLanguage($request->header('Accept-Language', ''));
        // 4、ua 语言
        $uaLang = StringHelper::getLangFromUA($request->header('User-Agent', ''));
        if ($lang) {
            $language = $lang;
        } elseif ($sessionLang) {
            $language = $sessionLang;
        } elseif ($cookieLang) {
            $language = $cookieLang;
        } elseif ($uaLang) {
            $language = $uaLang;
        }
//        loginfo('===lang===', ['language' => $language]);
        locale($language);
        return $handler($request);
    }
}