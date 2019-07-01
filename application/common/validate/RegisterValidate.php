<?php


namespace app\common\validate;


class RegisterValidate extends BaseValidate
{
    protected $rule = [
        "nickname" => "require",
        "mobile" => 'require|isMobile',
        "password" => 'require|length:6,12',
        "smsCode" => 'require',
    ];

    protected $message = [
        'nickname.require' => '缺少重要参数',
        'mobile.require' => '缺少重要参数',
        'mobile.isMobile' => '手机号不合法',
        'smsCode.require' => '验证码必填',
        'password.require' => '缺少重要参数',
    ];
}