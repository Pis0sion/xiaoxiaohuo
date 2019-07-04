<?php


namespace app\lib\enum;


class SmsEnum
{

    const SMSTYPES = [

        'register'      =>    '注册验证码',

        'forgetPwd'     =>    '忘记密码',

        'certification' =>    '实名认证',

        'bindBankCard'  =>    '绑定银行卡',
    ];
}