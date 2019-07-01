<?php


namespace app\common\validate;


class OrdersValidate extends BaseValidate
{
    protected $rule = [
        "number" => "require|integer",
    ];

    protected $message = [
        'number.require' => '缺少重要参数',
        'number.integer' => '参数类型不正确',
    ];
}