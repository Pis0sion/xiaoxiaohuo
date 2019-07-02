<?php


namespace app\api\repositories;


use app\api\utils\Utils;
use app\common\validate\OrdersValidate;
use app\lib\exception\ParameterException;

class IntegralRepositories
{
    /**
     * @param $malls
     * @return mixed
     */
    public function proList($malls)
    {
        $list = $malls->field('goods_id,goods_name,goods_price,goods_integral,goods_img')->paginate(10);
        return Utils::renderJson($list);
    }

    /**
     * 获取商品详情
     * @param $malls
     * @param \Closure $isExist
     * @return array
     */
    public function proDetails($malls,\Closure $isExist)
    {
        $isExist($malls);
        return Utils::renderJson($this->renderDetails($malls));
    }

    private function renderDetails($malls)
    {
        return (new class($malls){

            public $banners ;

            public $price ;

            public $goodName ;

            public $goodDesc ;

            /**
             *  constructor.
             * @param $malls
             */
            public function __construct($malls)
            {
                $this->banners = $malls->relationsToPics;
                $this->price = $malls->goods_price;
                $this->goodName = $malls->goods_name;
                $this->goodDesc = $malls->goods_desc;
            }

        });
    }

    /**
     * 预下单
     * @param $request
     * @param $malls
     * @param \Closure $isExistAccount
     * @param \Closure $isExist
     * @param \Closure $isEnough
     * @return array
     * @throws ParameterException
     */
    public function prepareOrders($request,$malls,\Closure $isExistAccount,\Closure $isExist,\Closure $isEnough)
    {
        (new OrdersValidate())->goCheck();
        //  检测用户账户
        $isExistAccount(app()->usersInfo->uAccount);
        //  检测商品
        $isExist($malls);
        //  检测库存
        $isEnough($request->number,$malls->goods_stock);
        //  检测积分
        $list = app()->Reflect->getFitMode(app()->usersInfo->uAccount->ua_integral_value,$request->number,$malls);

        if(count($list) > 0)
        {
            return Utils::renderJson(compact('list'));
        }

        throw new ParameterException(['msg' => '积分不足']);
    }

    // 下单
    public function placeOrders($request)
    {
        /**
         *  @params  地址  用户  商品  count   几园区  留言remark
         *
         */




    }




}