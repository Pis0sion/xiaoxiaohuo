<?php


namespace app\api\repositories;


use app\api\utils\Utils;
use app\common\validate\OrdersValidate;

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
     * @param $request
     * @param $malls
     * @param \Closure $isExist
     * @param \Closure $isEnough
     */
    public function prepareOrders($request,$malls,\Closure $isExist,\Closure $isEnough)
    {
        /**
         * 1  接受id  查询商品库存是否满足条件
         * 2  计算价格及相应的积分
         * 3
         */
        (new OrdersValidate())->goCheck();


        return app()->Reflect->getFitMode();


        $isExist($malls);
        $isEnough($request->number,$malls->goods_stock);



        return "123";

    }




}