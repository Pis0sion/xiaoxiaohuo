<?php


namespace app\lib\enum;


class PayChannels
{

    const CHANNELS = [
            "alipay"  => \app\api\service\payments\PayOfAliPay::class ,
            "wxpay"   => \app\api\service\payments\PayOfWxPay::class ,
        ];
}