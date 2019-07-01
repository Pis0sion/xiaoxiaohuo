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
        $list = $malls->field('goods_name,goods_price,goods_integral,goods_img')->paginate(10);
        return Utils::renderJson($list);
    }

    public function proDetails($malls)
    {

    }


}