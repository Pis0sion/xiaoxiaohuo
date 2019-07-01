<?php


namespace app\api\controller\v1;

use app\api\repositories\IntegralRepositories;
use app\common\model\IntegralMalls;

/**
 * 积分商城模块api
 * Class IntegralController
 * @package app\api\controller\v1
 */
class IntegralController
{
    /**
     * @var IntegralRepositories
     */
    protected  $integral ;

    /**
     * IntegralController constructor.
     * @param $integral
     */
    public function __construct(IntegralRepositories $integral)
    {
        $this->integral = $integral;
    }

    /**
     * 商品列表
     * @param IntegralMalls $malls
     * @return mixed
     * @route("api/v1/integral/list","get")
     *
     */
    public function getListByProducts(IntegralMalls $malls)
    {
        return $this->integral->proList($malls);
    }

    /**
     * @param IntegralMalls $malls
     * @return string
     * @route("api/v1/pro/:goods_id/details","post")
     * ->model('goods_id','\app\common\model\IntegralMalls',false)
     * ->middleware('token')
     *
     */
    public function productByDetails(IntegralMalls $malls)
    {
        return $this->integral->proDetails($malls);
    }
    /**
     * 预下单
     * @route("api/v1/place/preorders","post")
     *
     */
    public function placeOrders()
    {

    }


}