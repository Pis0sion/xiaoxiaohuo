<?php


namespace app\common\model;


use app\api\utils\Utils;
use app\lib\enum\Domain;
use app\lib\exception\ParameterException;
use think\Model;

class Goods extends Model
{

    protected $table = "axgy_goods";

    protected $pk = "g_id";

    /**
     * @param $value
     * @param $data
     * @return string
     */
    public function getGPhotoAttr($value,$data)
    {
        $path = "" ;
        if(!empty($value)){
            $path = Domain::BASEURL.$value ;
        }
        return $path ;
    }

    /**
     * @param $value
     * @param $data
     * @return string
     */
    public function getGDescAttr($value,$data)
    {
        if(!empty($value)){
            $value = '
<p>
<img style="width:100%;" src="https://wx.o7u11.cn/static/goods/4.png" title="4.png" alt="4.png"/>
<img style="width:100%;" src="https://wx.o7u11.cn/static/goods/5.png" title="5.png" alt="4.png"/>
</p>
';
        }
        return $value;
    }
    /**
     * @param $id
     * @param $from
     * @return array
     * @throws ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function detail($id, $from)
    {
        if ($from == 'home') {
            $map['g_on_sale'] = 1;
            $map['g_state'] = 1;
            $map['g_audit_state'] = 1;
            $field = 'g_id,g_name,g_sub_title,g_photo,g_desc,g_original_price,shop_id,attrId,platCateId,cateId,cateIds,g_price,g_stock,g_postage,g_spec_format,g_sales_volume,g_is_hot,g_is_recommend,g_is_preferential,g_is_index,g_is_activity,g_is_gold,g_is_feature,g_is_flash,feature_id,g_type,g_all_flash,g_residue_flash,g_start_time,g_end_time,g_activity_price';
        } else {
            $map['g_state'] = ['neq', 9];
            $field = '*';
        }
        $map['g_id'] = $id;
        $goodsDetail = self::where($map)->field($field)->find();
        if (!$goodsDetail) {
            throw new ParameterException(['msg' => "商品不存在！"]);
        }
        if ($from == 'home') {
            $goodsDetail['g_integral_price'] = 0;
            if ($goodsDetail['g_is_activity'] == 1) {
                $sagModel = new SysActivities();
                $field = 'a.sag_state,b.is_open,b.sa_flag,b.sa_start_time,b.sa_end_time';
                $activity_info = $sagModel->alias('a')->join('axgy_sys_activity b', 'a.sys_activity_id=b.sys_activity_id', 'left')->where(['a.goods_id' => $id, 'a.sag_state' => 1])->field($field)->find();
                if ($activity_info) {
                    if ($activity_info['is_open'] != 1) {
                        throw new ParameterException(['msg' => "该活动已过期! "]);
                    }
                    if ($activity_info['sa_flag'] == 'zero-dollars-to-buy') {
                        $goodsDetail['g_integral_price'] = $goodsDetail['g_activity_price'];
                        $goodsDetail['g_activity_price'] = 0;
                    } else if ($activity_info['sa_flag'] == 'hot-tip-goods') {
                        $goodsDetail['g_activity_price'] = $goodsDetail['g_activity_price'] - 10;
                        $goodsDetail['g_integral_price'] = 10;
                    }
                } else {
                    throw new ParameterException(['msg' => "该活动商品已过期 "]);
                }
            }
        }

        //商品相册
        $goodsPictureModel = new GoodsPics();
        $mapGp['goods_id'] = $id;
        $gPictrue = $goodsPictureModel->where($mapGp)->select();
        $goodsDetail['photoArr'] = $gPictrue;
        //商品SKU
        $gSkuModel = new GoodsSkus();
        $gSku = $gSkuModel->getGoodsSkus($id);
        if($gSku){
            foreach ($gSku as $key=>$value){
                if($value['vipPrice'] <= 0 || empty($value['vipPrice'])){
                    $gSku[$key]['vipPrice'] = $value['goodsPrice'];
                }
                if($value['activityPrice'] <= 0 || empty($value['activityPrice'])){
                    $gSku[$key]['activityPrice'] = $value['goodsPrice'];
                }
                $gSkuActivityPrice = $gSku[$key]['activityPrice'];
                if ($from == 'home') {
                    $gSku[$key]['integralPrice'] = 0;
                    if ($goodsDetail['g_is_activity'] == 1) {
                        if ($activity_info) {
                            if ($activity_info['sa_flag'] == 'zero-dollars-to-buy') {
                                $gSku[$key]['integralPrice'] = $gSkuActivityPrice;
                                $gSku[$key]['activityPrice'] = 0;
                            } else if ($activity_info['sa_flag'] == 'hot-tip-goods') {
                                $gSku[$key]['activityPrice'] = $gSkuActivityPrice - 10;
                                $gSku[$key]['integralPrice'] = 10;
                            }
                        }
                    }
                }
            }
        }
        $goodsDetail['skuArr'] = $gSku;
        //商品评价
        $cgModel = new CommentGoods();
        $mapCg['goods_id'] = $id;
        $mapCg['cg_state'] = 1;
        $field = 'cg_id,cg_content,cg_pictures,cg_goods_level,cg_shop_level,cg_express_level,cg_create_time';
        $comment = $cgModel->where($mapCg)->field($field)->order('cg_create_time', 'desc')->select();
        $goodsDetail['comment'] = $comment;
        return Utils::renderJson(['detail' => $goodsDetail]);
    }
}