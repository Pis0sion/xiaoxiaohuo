<?php


namespace app\common\model;


use think\Model;

class GoodsSkus extends Model
{

    protected $table = "axgy_goods_sku";

    protected $pk = "sku_id";

    /**
     * 获取sku
     */
    public function getGoodsSkus($goods_id)
    {
        return self::where('goods_id', $goods_id)->select();
    }

}