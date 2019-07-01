<?php


namespace app\common\model;


use think\Model;

class IntegralRelationToPics extends Model
{

    protected $table = "axgy_jfmall_goods_photo";

    protected $createTime = "create_time";

    protected $updateTime = false;

    protected $autoWriteTimestamp = true ;

    protected $pk = "photo_id";

    /**
     * @param $query
     */
    protected function base($query)
    {
        $query->order('sort desc');
    }


}