<?php


namespace app\common\validate;


class IsMobileValidate extends BaseValidate
{
    protected $rule = [
        "mobile" => "require|isMobile",
        "scene" => "require",
    ];

    protected $message = [
        'mobile.require' => '缺少重要参数',
        'mobile.isMobile' => '手机号不合法',
        'scene.require' => '缺少重要参数',
    ];
}