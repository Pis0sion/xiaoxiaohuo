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
        $list = $malls::with(['relationsToPics'=>function($query){
            $query->field('goods_id,img,sort');
        }])->select();
        return Utils::renderJson($list);
    }



}