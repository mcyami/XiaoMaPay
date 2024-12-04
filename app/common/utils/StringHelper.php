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

    /**
     * AES加密
     * @param string $str 待加密字符串
     * @return string
     */
    public static function aesEncrypt($str) {
        $iv = C('SYS_AES_IV');
        $key = C('SYS_AES_KEY');
        $encryptStr = '';
        $str = trim((string)$str);
        if (empty($str)) {
            return $encryptStr;
        }
        return base64_encode(openssl_encrypt($str, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv));
    }

    /**
     * AES解密
     * @param string $str 待解密字符串
     * @return string
     */
    public static function aesDecrypt($str) {
        $iv = C('SYS_AES_IV');
        $key = C('SYS_AES_KEY');
        $decryptStr = '';
        $str = trim((string)$str);
        if (empty($str)) {
            return $decryptStr;
        }
        return (string)openssl_decrypt(base64_decode($str), 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }

    /**
     * 手机号掩码
     * @param string $mobile
     * @return string
     */
    public static function maskMobile($mobile) {
        return self::hiddenMiddle($mobile, 3, 4);
    }

    /**
     * 隐藏字符串中间指定位数
     * @param string $str 待处理字符串
     * @param int $headShow 头部显示位数
     * @param int $tailShow 尾部显示位数
     * @param string $replace 替换字符
     * @return string
     */
    public static function hiddenMiddle($str, $headShow, $tailShow, string $replace = '*'): string {
        if (empty($str)) {
            return '';
        }
        // 需要替换的字符数
        $replaceCount = mb_strlen($str) - $headShow - $tailShow;
        if ($replaceCount <= 0) {
            return $str;
        }
        $middle = str_repeat($replace, $replaceCount);
        $tempStr = mb_substr($str, 0, $headShow) . $middle;
        if ($tailShow > 0) {
            $tempStr .= mb_substr($str, -$tailShow);
        }
        return $tempStr;
    }
}