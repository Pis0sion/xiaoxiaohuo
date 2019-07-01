<?php


namespace app\common\model;

use think\Model;

/**
 * Class ProfitLogs
 * @package app\common\model
 */
class ProfitLogs extends Model
{

    protected $table = "axgy_profit_log";

    protected $createTime = "pl_create_time";

    protected $updateTime = false ;

    protected $autoWriteTimestamp = "datetime" ;

    protected $pk = "pl_id";

    /**
     * @return \think\model\relation\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(Users::class,"pl_buyer_id","id")->bind('u_nickname');
    }

    /**
     * @param $value
     * @param $data
     * @return string
     */
    public function getEarningsTypeAttr($value,$data)
    {
        if($value == "direct")
        {
            $value = "直接推荐";
        }elseif ($value == "indirect"){
            $value = "间接推荐";
        }
        return $value ;
    }
}