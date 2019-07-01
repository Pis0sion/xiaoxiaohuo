<?php


namespace app\common\validate;


class WithdrawValidate extends BaseValidate
{
    protected $rule = [
        "bc_id"  => "require|number",
        "money" => "require",
    ];

    protected $message = [
        'bc_id.require' => '缺少重要参数',
        'bc_id.number'  => '参数不合法',
        'money.require' => '缺少重要参数',
    ];
}