<?php


namespace app\api\controller\v1;

use app\api\repositories\NotifyRepositories;

/**
 * Class NotifyController
 * @package app\api\controller\v1
 */
class NotifyController
{

    protected $notify ;

    /**
     * NotifyController constructor.
     * @param $notify
     */
    public function __construct(NotifyRepositories $notify)
    {
        $this->notify = $notify;
    }

    /**
     * 订单回调
     * @return mixed
     * @throws \ReflectionException
     * @throws \app\lib\exception\ParameterException
     * @route("api/v1/orders/notify","post")
     *
     */
    public function doOrderNotify()
    {
        return $this->notify->doOrderNotify();
    }


}