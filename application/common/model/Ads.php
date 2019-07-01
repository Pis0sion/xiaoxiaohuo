<?php


namespace app\common\model;


use app\lib\enum\Domain;
use think\Model;

class Ads extends Model
{

    protected $table = "axgy_ad";

    protected $pk = "ad_id" ;

    protected $createTime = "ad_create_time";

    protected $updateTime = false ;

    protected $autoWriteTimestamp = "datetime" ;

    /**
     * @param $value
     * @param $data
     * @return string
     */
    public function getAdImgAttr($value, $data)
    {
        $path = "";
        if(!empty($value)) {
            $path = Domain::BASEURL.$value;
        }
        return $path ;
    }
}