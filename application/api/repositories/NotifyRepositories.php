<?php


namespace app\api\repositories;

use app\api\service\PayService;
use app\common\model\OrdersByIntegral;

/**
 * Class NotifyRepositories
 * @package app\api\repositories
 */
class NotifyRepositories
{
    /**
     * 处理回调
     * @return mixed
     * @throws \ReflectionException
     * @throws \app\lib\exception\ParameterException
     */
    public function doOrderNotify()
    {
        return (new PayService())->payNotify(function($formNo){

            /**
             * 修改订单的状态
             */
            $orders = OrdersByIntegral::where('order_sn',$formNo)->lock(true)->findOrFail();

            $orders->order_status = 20 ;

            $orders->save();

        },function(){

            // TODO: 记录日志

        });
    }
}