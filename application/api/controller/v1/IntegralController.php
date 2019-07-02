<?php


namespace app\api\controller\v1;

use app\api\repositories\IntegralRepositories;
use app\common\model\IntegralMalls;
use app\lib\exception\ParameterException;
use think\Request;

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
     * 商品详情
     * @param IntegralMalls $malls
     * @return array
     * @route("api/v1/pro/:goods_id/details","post")
     * ->model('goods_id','\app\common\model\IntegralMalls',false)
     * ->middleware('token')
     *
     */
    public function productByDetails(IntegralMalls $malls)
    {
        return $this->integral->proDetails($malls,function($malls){
            if($malls->isEmpty())
                throw new ParameterException();
        });
    }

    /**
     *  预下单
     * @param Request $request
     * @param IntegralMalls $malls
     * @return array
     * @throws ParameterException
     * @route("api/v1/prepare/:goods_id/orders","post")
     * ->model('goods_id','\app\common\model\IntegralMalls',false)
     * ->middleware('token')
     *
     */
    public function prepareToPlaceOrders(Request $request,IntegralMalls $malls)
    {
        return $this->integral->prepareOrders($request,$malls,function ($account){
            if(!$account)
                throw new ParameterException(['msg' => '用户无法购买']);
        },function($malls){
            if($malls->isEmpty())
                throw new ParameterException(['msg' => '该商品不存在']);
        },function($num,$stock){
            if($num >= $stock)
                throw new ParameterException(['msg' => '库存不足']);
        });
    }

    /**
     * 下单
     * @route("api/v1/place/:goods_id/orders","post")
     * ->middleware('token')
     *
     */
    public function placeOrders(Request $request)
    {
        return $this->integral->placeOrders($request);
    }


}