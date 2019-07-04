<?php


namespace app\api\repositories;

use app\api\service\PayService;
use app\api\utils\Utils;
use app\common\model\Accounts;
use app\common\model\IntegralMalls;
use app\common\model\OrdersByIntegral;
use app\common\validate\PayOrdersValidate;
use app\lib\exception\ParameterException;
use think\Db;
use think\facade\Log;

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
     * @param \Closure $isEnough
     * @return array
     * @throws ParameterException
     * @throws \Throwable
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function payOrders($request,\Closure $isEnough)
    {
        (new PayOrdersValidate())->goCheck();

        $order_sn = $request->order_sn ;

        //  获取该订单的状态   只有待支付状态
        $order = OrdersByIntegral::where("order_sn",$order_sn)
            ->where('order_sn',10)->findOrEmpty();

        if($order->isEmpty()) {
            throw new ParameterException(['msg' => '订单状态不正确']);
        }

        //  获取商品id
        $goods_id = $order->hasManyIntegralGoods->goods_id ;
        //  购买数量
        $purchase_count = $order->hasManyIntegralGoods->num ;

        Db::startTrans();

        try{
            //  查询该商品的库存量
            $stock = IntegralMalls::where("goods_id",$goods_id)->lock(true)->value('goods_stock');
            //  检测库存量
            $isEnough($stock,$purchase_count);
            //  查询该用户积分
            $userIntegral = Accounts::where('uid',$order->user_id)->lock(true)->value('ua_integral_value');
            //  检测积分
            $isEnough($userIntegral,$order->order_integral);
            //  吊起支付
            $payUrl = (new PayService())->payAction($request->type , $order);

            if($payUrl) {
                //  提交事务
                Db::commit();

                return Utils::renderJson(compact('payUrl'));
            }
        }catch (\Throwable $e) {

            Db::rollback();

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