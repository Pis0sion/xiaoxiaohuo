<?php


namespace app\api\service\payments;


use app\common\model\OrdersByIntegral;

abstract class IPayChannels
{
    /**
     * @var
     * 网关
     */
    protected $gateWay ;

    /**
     * IPayChannels constructor.
     */
    public function __construct()
    {
        $this->gateWay = $this->init();
    }


    abstract public function init();
    /**
     * @param OrdersByIntegral $orders
     * @return mixed
     */
    abstract public function payOrder(OrdersByIntegral $orders);

    /**
     * @param \Closure $success
     * @param \Closure $fail
     * @return mixed
     */
    abstract function doNotify(\Closure $success,\Closure $fail);

}