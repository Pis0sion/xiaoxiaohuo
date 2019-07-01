<?php


namespace app\common\model;


use think\Model;

/**
 * 收货地址
 * Class UserConsigns
 * @package app\common\model
 */
class UserConsigns extends Model
{

    protected $table = "axgy_user_consign";

    protected $createTime = "uc_create_time";

    protected $updateTime = false ;

    protected $autoWriteTimestamp = "datetime" ;



}