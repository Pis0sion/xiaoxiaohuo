<?php


namespace app\common\validate;


class OrdersValidate extends BaseValidate
{
    protected $rule = [
        "count" => "require|egt:1",
        "mid" => "require|integer",
    ];

    protected $message = [
        'count.require' => '缺少重要参数',
        'count.egt' => '参数类型不正确',
        'mid.require' => '缺少重要参数',
    ];
}