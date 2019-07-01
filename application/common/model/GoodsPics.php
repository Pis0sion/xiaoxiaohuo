<?php


namespace app\common\model;


use app\lib\enum\Domain;
use think\Model;

class GoodsPics extends Model
{

    protected $table = "axgy_goods_picture";

    protected $pk = "gp_id";

    /**
     * @param $value
     * @param $data
     * @return string
     */
    public function getGpNameAttr($value,$data)
    {
        $path = "" ;
        if(!empty($value)){
            $path = Domain::BASEURL.$value ;
        }
        return $path ;
    }
}