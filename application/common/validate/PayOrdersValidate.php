<?php


namespace app\common\validate;


class PayOrdersValidate extends BaseValidate
{
    protected $rule = [
        "type" => "require",
        "order_sn" => "require",
    ];

    protected $message = [
        'type.require' => '缺少重要参数',
        'order_sn.require' => '缺少重要参数',
    ];
}