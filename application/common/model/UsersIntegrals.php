<?php


namespace app\common\model;


use think\Model;

class UsersIntegrals extends Model
{

    protected $table = "axgy_user_integral_list";

    protected $pk = "user_integral_list_id";

    protected $createTime = "createtime";

    protected $updateTime = false ;

    protected $autoWriteTimestamp = "datetime" ;

}