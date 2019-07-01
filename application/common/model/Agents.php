<?php


namespace app\common\model;


use think\Model;

/**
 * Class Agents
 * @package app\common\model
 */
class Agents extends Model
{
    protected $table = "axgy_user_agent";

    protected $createTime = "ua_create_time";

    protected $updateTime = false ;

    protected $autoWriteTimestamp = "datetime" ;

    protected $pk = "ua_id";

    /**
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getUaLevelAttr($value,$data)
    {
        $gradesText = [
            'basics' => '游客' ,
            'manager' => '供应商' ,
            'partner' => '总供应商' ,
			'city'=>"总供应商",
			'county'=>"总供应商",
			'province'=>"总供应商",

        ];
        return $gradesText[$value];
    }

}