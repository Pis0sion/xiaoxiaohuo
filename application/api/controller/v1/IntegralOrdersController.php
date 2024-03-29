<?php


namespace app\api\controller\v1;


use app\api\repositories\OrdersByIntegralRepositories;
use app\lib\exception\ParameterException;
use think\Request;

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
     * 支付
     * @param Request $request
     * @return array
     * @throws ParameterException
     * @throws \Throwable
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @route("api/v1/order/pay","post")
     * ->middleware('token')
     *
     */
    public function payAction(Request $request)
    {
        return $this->ordersRepositories->payOrders($request);
    }

    /**
     * 查询订单
     * @param Request $request
     * @return mixed
     * @route("api/v1/check/orders","post")
     * ->middleware('token')
     *
     */
    public function userOrdersByStatus(Request $request)
    {
        return $this->ordersRepositories->getAllOrderByConditions($request);
    }

    // 查询订单详情
    public function orderDetailsByUsers()
    {
        return $this->ordersRepositories->getOrderDetails();
    }

}