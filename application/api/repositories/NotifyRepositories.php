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

        });
    }
}