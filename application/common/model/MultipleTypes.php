<?php


namespace app\common\model;


use think\Model;

class MultipleTypes extends Model
{

    protected $table = "axgy_jfmall_rules";

    protected $createTime = "tp_create_time";

    protected $updateTime = "tp_update_time";

    protected $autoWriteTimestamp = true ;

    protected function base($query)
    {
        $query->order('tp_sort desc');
    }

}