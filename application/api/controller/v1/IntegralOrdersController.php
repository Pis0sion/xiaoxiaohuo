<?php


namespace app\api\controller\v1;


use app\api\repositories\OrdersByIntegralRepositories;

/**
 * 订单控制器
 * Class IntegralOrdersController
 * @package app\api\controller\v1
 */
class IntegralOrdersController
{
    /**
     * 订单仓库
     * @var OrdersByIntegralRepositories
     */
    protected $ordersRepositories ;

    /**
     * IntegralOrdersController constructor.
     * @param $ordersRepositories
     */
    public function __construct(OrdersByIntegralRepositories $ordersRepositories)
    {
        $this->ordersRepositories = $ordersRepositories;
    }

    


}