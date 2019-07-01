<?php


namespace app\common\model;


use think\Model;

class AdsPositions extends Model
{

    protected $table = "axgy_ad_position";

    protected $pk = "ap_id" ;

    protected $createTime = "ap_create_time";

    protected $updateTime = false ;

    protected $autoWriteTimestamp = "datetime" ;

    /**
     * 广告位
     * @return \think\model\relation\HasMany
     */
    public function hasAds()
    {
        return $this->hasMany(Ads::class,"ap_id","ap_id");
    }

    /**
     * 地址的广告位
     * @param $positions
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function adsToPositions($positions)
    {
        return $this->hasAds()->where($positions)->order('ad_rank asc')->select();
    }



}