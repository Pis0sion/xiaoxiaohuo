<?php


namespace app\common\model;


use think\Model;

/**
 * 实名认证
 * Class Certifications
 * @package app\common\model
 */
class Certifications extends Model
{

    protected $table = "axgy_user_certification";

    protected $createTime = "ucer_create_time";

    protected $updateTime = false ;

    protected $autoWriteTimestamp = "datetime" ;

    protected $pk = "ucer_id";

}