<?php


namespace app\api\repositories;

use app\common\validate\PayOrdersValidate;

/**
 * 订单仓库
 * Class OrdersByIntegralRepositories
 * @package app\api\repositories
 */
class OrdersByIntegralRepositories
{

    public function getOrderInfo()
    {
        return "456789456789456789";
    }
    // 支付
    public function payOrders()
    {
        (new PayOrdersValidate())->goCheck();

    }
}