<?php

namespace app\common\driver\monipay;

class monipay {
    static public $info = [
        'key' => 'monipay', // 支付驱动英文名称，需和目录名称一致，不能有重复
        'name' => '模拟支付', // 支付驱动显示名称
        'author' => 'XiaoMaPay', // 支付驱动作者
        'link' => 'https://www.xiaomapay.com/', // 支付驱动作者链接
        'pay_types' => ['alipay', 'qqpay', 'wxpay', 'bank', 'jdpay'], // 支付驱动支持的支付方式，可选的有alipay,qqpay,wxpay,bank
        'trans_types' => ['alipay', 'bank'], // 支付驱动支持的付款方式，可选的有alipay,qqpay,wxpay,bank
        'inputs' => [ // 支付驱动要求传入的参数以及参数显示名称，可选的有appid,appkey,appsecret,appurl,appmchid
            'appurl' => [
                'name' => '接口地址',
                'type' => 'input',
                'note' => '必须以http://或https://开头，以/结尾',
            ],
            'appid' => [
                'name' => '商户ID',
                'type' => 'input',
                'note' => '',
            ],
            'appkey' => [
                'name' => '商户密钥',
                'type' => 'input',
                'note' => '',
            ],
            'appswitch' => [
                'name' => '是否使用mapi接口',
                'type' => 'select',
                'options' => [0 => '否', 1 => '是'],
            ],
        ],
        'select' => null,
        'note' => '', // 支付密钥填写说明
        'bindwxmp' => false, // 是否支持绑定微信公众号
        'bindwxa' => false, // 是否支持绑定微信小程序
    ];

    static public function submit() {
        return json(['code' => -1, 'msg' => '支付接口未配置']);
    }

}