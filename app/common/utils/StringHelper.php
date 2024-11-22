<?php

namespace app\common\utils;

/**
 * 字符串处理工具类
 */
class StringHelper {


    /**
     * 通过UA获取语言
     * @param $ua
     * @return string
     */
    public static function getLangFromUA($ua): string {
        $match = [];
        preg_match("/(lang\/\w+)|([Ll]anguage\/\w+)/", $ua, $match);
        if (empty($match)) {
            return "";
        } else {
            return (string)substr($match[0], strpos($match[0], '/') + 1);
        }
    }

    /**
     * 通过accept-language获取语言
     */
    public static function getLangFromAcceptLanguage($acceptLanguage): string {
        // 示例accept-language：zh-CN,zh;q=0.9,en;q=0.8
        $match = [];
        // 匹配第一个语言表示，如zh-CN，zh，en 含有-的表示带地区信息
        preg_match("/\w+(-\w+)?/", $acceptLanguage, $match);
        if (empty($match)) {
            return "";
        } else {
            return (string)$match[0];
        }
    }
}