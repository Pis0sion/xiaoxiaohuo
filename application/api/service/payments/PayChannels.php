<?php


namespace app\api\service\payments;


use app\common\model\OrdersByIntegral;

class PayChannels
{
    /**
     * @var IPayChannels
     */
    protected $payChannel ;

    /**
     * PayChannels constructor.
     * @param $payChannel
     */
    public function __construct(IPayChannels $payChannel)
    {
        $this->payChannel = $payChannel;
    }

    /**
     * 购买
     * @param OrdersByIntegral $order
     * @return mixed
     */
    public function purchase(OrdersByIntegral $order)
    {
        return $this->payChannel->payOrder($order);
    }

    /**
     * 处理回调
     * @param \Closure $success
     * @param \Closure $fail
     * @return mixed
     */
    public function doNotify(\Closure $success,\Closure $fail)
    {
        return $this->payChannel->doNotify($success,$fail);
    }

}