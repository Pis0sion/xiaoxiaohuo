<?php


namespace app\common\model;


use think\Model;

/**
 * Class IntegralMalls
 * @package app\common\model
 */
class IntegralMalls extends Model
{

    protected $table = "axgy_jfmall_goods";

    protected $createTime = "create_time";

    protected $updateTime = "update_time";

    protected $autoWriteTimestamp = true ;

    protected $pk = "goods_id";

    /**
     * @param $query
     */
    protected function base($query)
    {
        $query->order('goods_sort desc');
    }

    /**
     * 关联商品图片
     * @return \think\model\relation\HasMany
     */
    public function relationsToPics()
    {
        return $this->hasMany(IntegralRelationToPics::class,"goods_id","goods_id");
    }

    /**
     * 新增商品的图片
     * @param $pics
     * @return array|false
     */
    public function addRelationsToPics($pics)
    {
        return $this->relationsToPics()->saveAll($pics);
    }





}