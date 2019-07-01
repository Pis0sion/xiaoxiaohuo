<?php


namespace app\common\validate;


class CertificationValidate extends BaseValidate
{
    protected $rule = [
        "realname" => "require",
        "idcard" => 'require|idCard',
        "mobile" => 'require|isMobile',
        "smsCode" => 'require',
    ];

    protected $message = [
        'realname.require' => '缺少重要参数',
        'idcard.require' => '缺少重要参数',
        'idcard.idCard' => '请输入正确的身份证号',
        'mobile.require' => '缺少重要参数',
        'mobile.isMobile' => '请输入正确的手机号',
        'smsCode.require' => '缺少重要参数',
    ];
}