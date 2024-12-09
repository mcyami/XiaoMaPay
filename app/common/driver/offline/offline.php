<?php

namespace app\common\driver\offline;

class offline {
    static public $info = [
        'key' => 'offline', // 支付驱动英文名称，需和目录名称一致，不能有重复
        'name' => '线下支付', // 支付驱动显示名称
        'author' => 'XiaoMaPay', // 支付驱动作者
        'link' => 'https://www.xiaomapay.com/', // 支付驱动作者链接
        'pay_types' => ['offline', 'monipay'], // 支付驱动支持的支付方式，可选的有alipay,qqpay,wxpay,bank
        'trans_types' => ['offline', 'monipay'], // 支付驱动支持的付款方式，可选的有alipay,qqpay,wxpay,bank
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
                'note' => '33333333',
            ],
            'appswitch' => [
                'name' => '是否使用mapi接口',
                'type' => 'select',
                'options' => [0 => '否', 1 => '是'],
                'note' => '2232',
            ],
            'apptest1' => [
                'name' => '商户test1',
                'type' => 'textarea',
                'note' => '12333',
            ],
        ],
        'select' => [ //选择已开启的支付形式
            '1' => '电脑网站支付',
            '2' => '手机网站支付',
            '3' => '当面付扫码'
        ],
        'note' => '<p>在支付宝服务商后台进件后可获取到子商户的授权链接，子商户访问之后即可得到商户授权token。</p><p>如果使用公钥证书模式，需将<font color="red">应用公钥证书、支付宝公钥证书、支付宝根证书</font>3个crt文件放置于<font color="red">/plugins/alipaysl/cert/</font>文件夹（或<font color="red">/plugins/alipaysl/cert/应用APPID/</font>文件夹）</p>', // 支付密钥填写说明
        'bind_wxmp' => 0, // 是否支持绑定微信公众号
        'bind_wxa' => 0, // 是否支持绑定微信小程序
    ];

    static public function submit() {
        return json(['code' => -1, 'msg' => '支付接口未配置']);
    }

}