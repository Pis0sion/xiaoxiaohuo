<?php


namespace app\api\repositories;


use app\api\utils\Utils;

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

    public function proDetails($malls)
    {
        return $this->renderDetails($malls);
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


}