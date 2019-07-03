<?php


namespace app\api\controller\v1;


use app\api\repositories\OrdersByIntegralRepositories;
use app\api\service\PayService;
use app\common\model\OrdersByIntegral;
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

    public function demo()
    {
        $type = "alipay";
        $order = OrdersByIntegral::get(30);
        return  (new PayService())->payAction($type,$order);
    }

    /**
     * 支付
     * @param Request $request
     * @return array
     * @throws ParameterException
     * @route("api/v1/order/pay","post")
     *
     */
    public function payAction(Request $request)
    {
        return $this->ordersRepositories->payOrders($request,function($num1,$num2){
            if ($num1 < $num2)
                throw new ParameterException(['msg' => '用户积分不足，或者商品已买完']);
        });
    }



}