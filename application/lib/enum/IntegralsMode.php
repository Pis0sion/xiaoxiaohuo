<?php


namespace app\lib\enum;


class IntegralsMode
{

    const MODE = [

        "one"   => \app\api\service\integrals\IntegralOfModeOne::class,
        "two"   => \app\api\service\integrals\IntegralOfModeTwo::class,
        "three" => \app\api\service\integrals\IntegralOfModeThree::class,

    ];
}