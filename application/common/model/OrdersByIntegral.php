<?php


namespace app\common\model;


use think\Model;

/**
 * 积分订单表
 * Class OrdersByIntegral
 * @package app\common\model
 */
class OrdersByIntegral extends Model
{

    protected $table = "axgy_jfmall_order";

    protected $createTime = "create_time";

    protected $updateTime = "update_time";

    protected $autoWriteTimestamp = true ;

    protected $pk = "order_id";

    /**
     * 订单关联的商品
     * @return \think\model\relation\HasMany
     */
    public function hasManyIntegralGoods()
    {
        return $this->hasMany(OrdersRelationsByGoods::class,"order_id",'order_id');
    }


}