<?php


namespace app\api\controller\v1;


use app\api\repositories\OrdersByIntegralRepositories;
use app\api\service\PayService;
use app\common\model\OrdersByIntegral;

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
    /**
     * @route("api/v1/order/pay","get")
     *
     */
    public function demo()
    {
        $type = "alipay";
        $order = OrdersByIntegral::get(30);
        return  (new PayService())->payAction($type,$order);
    }

    /**
     *
     */
    public function payAction()
    {
        return $this->ordersRepositories->payOrders();
    }



}