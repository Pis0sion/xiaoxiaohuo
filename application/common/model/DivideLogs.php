<?php


namespace app\common\model;


use think\Model;

class DivideLogs extends Model
{

    protected $table = "axgy_user_divide_log" ;

    protected $createTime = "create_time";

    protected $updateTime = false ;

    protected $autoWriteTimestamp = "datetime" ;

    protected $pk = "user_divide_log_id";

}