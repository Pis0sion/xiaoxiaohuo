<?php


namespace app\common\validate;


class ConsignsValidate extends BaseValidate
{
    protected $rule = [
        "uc_consignee" => "require",
        "uc_phone" => 'require|isMobile',
        "uc_county" => 'require|number',
        "uc_location" => 'require',
        "is_default" => 'require|in:0,1',
    ];

    protected $message = [
        'uc_consignee.require' => '缺少重要参数',
        'uc_phone.require' => '缺少重要参数',
        'uc_phone.isMobile' => '参数不合法',
        'uc_county.require' => '缺少重要参数',
        'uc_county.number' => '参数不合法',
        'uc_location.require' => '缺少重要参数',
        'is_default.require' => '缺少重要参数',
    ];
}