<?php


namespace app\common\validate;


class NickNameValidate extends BaseValidate
{
    protected $rule = [
        "nickname" => "require",
    ];

    protected $message = [
        'nickname.require' => '缺少重要参数',
    ];
}