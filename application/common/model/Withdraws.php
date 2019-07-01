<?php


namespace app\common\model;


use think\Model;

class Withdraws extends Model
{

    protected $table = "axgy_withdraw_order";

    protected $createTime = "wo_create_time";

    protected $updateTime = false ;

    protected $autoWriteTimestamp = "datetime" ;

    protected $pk = "wo_id";

    /**
     * @return \think\model\relation\BelongsTo
     */
    public function bank()
    {
        return $this->belongsTo(BindBankCards::class,"bc_id","ubc_id")->bind("ubc_name,ubc_num,ubc_holder");
    }

    /**
     * @param $value
     * @param $data
     * @return string
     */
    public function getWoAuditStateAttr($value,$data)
    {
        $result = "待审核";
        if($value == 1){
            $result = "审核通过";
        }
        if($value == 2){
            $result = "审核拒绝";
        }
        return $result ;
    }


}