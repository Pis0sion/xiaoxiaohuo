<?php


namespace app\api\repositories;

use app\api\service\PayService;
use app\api\utils\Utils;
use app\common\model\OrdersByIntegral;
use app\common\validate\PayOrdersValidate;
use app\lib\exception\ParameterException;

/**
 * 订单仓库
 * Class OrdersByIntegralRepositories
 * @package app\api\repositories
 */
class OrdersByIntegralRepositories
{
    /**
     * 支付
     * @param $request
     * @return array
     * @throws ParameterException
     * @throws \Throwable
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function payOrders($request)
    {
        (new PayOrdersValidate())->goCheck();

        $order_sn = $request->order_sn ;

        //  获取该订单的状态   只有待支付状态
        $order = OrdersByIntegral::where("order_sn",$order_sn)
            ->where('order_status',10)->findOrEmpty();

        if($order->isEmpty()) {
            throw new ParameterException(['msg' => '订单状态不正确']);
        }

        try{
            //  吊起支付
            $payUrl = (new PayService())->payAction($request->type , $order);

            return Utils::renderJson(compact('payUrl'));

        }catch (\Throwable $e) {

            if($e instanceof ParameterException)

                throw $e ;
        }

        throw new ParameterException(['msg' => '支付失败']);

    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllOrderByConditions($request)
    {
        $order_status = $request->status ;

        $orders = app()->usersInfo->hasIntegralOrders()
            ->field("order_id,order_sn,order_status,goods_price,order_amount,order_integral,create_time");

        if($order_status) {
            $orders = $orders->where('order_status',$order_status);
        }

        return Utils::renderJson(Utils::render()->call($orders->paginate(10)));

    }



}