<?php


namespace app\common\validate;


class PlaceOrdersValidate extends BaseValidate
{

    protected $rule = [
        "count" => "require|egt:1",
        "goods_id" => "require|egt:1",
        "mid" => "require|integer",
        "uc_id" => "require|integer",
    ];

    protected $message = [
        'count.require' => '缺少重要参数',
        'goods_id.require' => '缺少重要参数',
        'goods_id.egt' => '缺少重要参数',
        'count.egt' => '参数类型不正确',
        'mid.require' => '缺少重要参数',
        "uc_id.require" => "缺少重要参数",
        "uc_id.integer" => "参数类型不正确",
    ];

}