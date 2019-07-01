<?php


namespace app\common\model;


use think\Model;

class Regions extends Model
{
    protected $table = "axgy_region";

    /**
     * 是否存在
     * @param $id
     * @return mixed
     */
    public function isExist($id)
    {
        return self::get($id);
    }
}