<?php


namespace app\common\model;


use think\Model;

class GoodsCategorys extends Model
{

    protected $table = "axgy_goods_category";

    protected $pk = "gc_id";

    protected $createTime = "g_create_time";

    protected $updateTime = false ;

    protected $autoWriteTimestamp = "datetime" ;
    /**
     * @param array $where
     * @param string $field
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_gc_list($where = [], $field = '*')
    {
        return self::where($where)->order('gc_rank asc')->field($field)->select();
    }

    /**
     * 归类商品
     * @return \think\model\relation\HasMany
     */
    public function manyGoods()
    {
        return $this->hasMany(Goods::class,"platCateId","gc_id");
    }

    /**
     * 商品列表
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function hasGoods()
    {
        $map['g_on_sale'] = 1;
        $map['g_state'] = 1;
        $map['g_is_flash'] = 0;
        $map['g_is_activity'] = 0;
        return $this->manyGoods()->where($map)->field('g_id,g_name,g_sub_title,g_photo,g_price,vip_price,g_type,g_sales_volume,g_original_price,g_all_flash,g_residue_flash')->paginate(10);
    }

}