<?php


namespace app\common\validate;


class EntryValidate extends BaseValidate
{
    protected $rule = [
        "nickname" => "require",
        "password" => 'require',
    ];

    protected $message = [
        'mobile.require' => '缺少重要参数',
        'password.require' => '缺少重要参数',
    ];

}