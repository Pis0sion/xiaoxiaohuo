<?php


namespace app\common\validate;


class PlaceOrdersValidate extends BaseValidate
{

    protected $rule = [
        "number" => "require|egt:1",
        "goods_id" => "require|egt:1",
        "type" => "require|integer",
    ];

    protected $message = [
        'number.require' => '缺少重要参数',
        'goods_id.require' => '缺少重要参数',
        'goods_id.egt' => '缺少重要参数',
        'number.egt' => '参数类型不正确',
        'type.require' => '缺少重要参数',
    ];

}