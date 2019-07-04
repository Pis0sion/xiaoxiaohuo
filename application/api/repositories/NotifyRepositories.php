<?php


namespace app\api\repositories;

use app\api\service\PayService;

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


        },function(){

        });
    }
}