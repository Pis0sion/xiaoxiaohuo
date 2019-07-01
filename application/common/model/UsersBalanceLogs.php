<?php


namespace app\common\model;


use think\Model;

class UsersBalanceLogs extends Model
{

    protected $table = "axgy_user_log_balance";

    protected $createTime = "createtime";

    protected $updateTime = false ;

    protected $autoWriteTimestamp = "datetime" ;

    public function getSymbolAttr($value,$data)
    {
        if($value == 1)
        {
            $value = "结算货款";
        }

        return $value ;
    }

}