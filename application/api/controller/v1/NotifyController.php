<?php


namespace app\api\controller\v1;

use app\api\repositories\NotifyRepositories;
use think\Request;

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
     * @param Request $request
     * @route("api/v1/orders/notify","post")
     *
     */
    public function doOrderNotify()
    {
        return $this->notify->doOrderNotify();
    }


}