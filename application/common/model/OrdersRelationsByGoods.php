<?php


namespace app\common\model;


use think\Model;

/**
 * 订单商品关联表
 * Class OrdersRelationsByGoods
 * @package app\common\model
 */
class OrdersRelationsByGoods extends Model
{

    protected $table = "axgy_jfmall_order_goods";

    protected $createTime = "create_time";

    protected $updateTime = false;

    protected $autoWriteTimestamp = true ;

    /**
     * 订单关联商品
     * @return \think\model\relation\HasOne
     */
    public function relationsToGoodsByAttributes()
    {
        return $this->hasOne(IntegralMalls::class,"goods_id","goods_id");
    }


}