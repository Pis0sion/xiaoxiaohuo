<?php


namespace app\common\validate;


class BindBankCardValidate extends BaseValidate
{

    protected $rule = [
        "mobile" => "require|isMobile",
        "smsCode" => 'require',
        "holder" => 'require',
        "bankId" => 'require',
        "cardNum" => 'require',
        "cardNumConfirm" => 'require|confirm:cardNum',
        'countyId' =>'require',
        "bankBranch" => 'require',
    ];

    protected $message = [
        'mobile.require' => '缺少重要参数',
        'mobile.isMobile' => '请输入正确的手机号',
        'smsCode.require' => '缺少重要参数',
        'holder.require' => '缺少重要参数',
        'bankId.require' => '缺少重要参数',
        'cardNum.require' => '缺少重要参数',
        'countyId.require' => '缺少重要参数',
        'bankBranch.require' => '缺少重要参数',
    ];
}