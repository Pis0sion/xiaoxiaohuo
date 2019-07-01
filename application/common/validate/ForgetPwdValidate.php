<?php


namespace app\common\validate;


class ForgetPwdValidate extends BaseValidate
{
    protected $rule = [
        "mobile"  => "require|isMobile",
        "nickname" => "require",
        "smsCode" => "require",
        "password" => 'require',
        'repassword'=>'require|confirm:password'
    ];

    protected $message = [
        'mobile.require' => '缺少重要参数',
        'mobile.isMobile' => '手机号不合法',
        'nickname.require' => '缺少重要参数',
        'smsCode.require' => '缺少重要参数',
        'password.require' => '缺少重要参数',
    ];
}