<?php


namespace app\api\repositories;


use app\api\utils\Utils;

/**
 * Class HomeRepositories
 * @package app\api\repositories
 */
class HomeRepositories
{
    /**
     * @param $adsPositions
     * @return array
     */
    public function getBanner($adsPositions)
    {
        $positions = ['ap_flag' => 'index-banner'];
        $banner = $adsPositions->where($positions)->find();
        $banners = $banner->adsToPositions(['ad_state' => 1]);
        return Utils::renderJson(compact('banners'));
    }

    /**
     * @param $categorys
     * @param $goods
     * @param $adsPositions
     * @return array
     */
    public function getProducts($categorys,$goods,$adsPositions)
    {
        $cate_map = ['gc_state' => 1, 'is_show' => 1,'ios_is_show' => 1 ];
        $cate_list = $categorys->get_gc_list($cate_map);
        foreach ($cate_list as $key => $value) {
            $map['platCateId'] = $value['gc_id'];
            $map['g_on_sale'] = 1;
            $map['g_state'] = 1;
            $map['g_is_index'] = 1;
            $map['g_is_hot'] = 0;
            $map['g_is_preferential'] = 0;
            $map['g_is_recommend'] = 0;
            $goodsList = $goods->where($map)->field('g_id,g_name,g_sub_title,g_photo,g_price,vip_price,g_type,g_sales_volume')->order('g_rank asc')->select();
            $cate_list[$key]['goods'] = $goodsList;
        }

        //热卖
        $positions = ['ap_flag' => 'indexnew-hot'];
        $hot = $adsPositions->where($positions)->find();
        $hots = $hot->adsToPositions(['ad_state' => 1])->toArray();

        $goods_hot = $hots[0];
        $hot_map['g_on_sale'] = 1;
        $hot_map['g_state'] = 1;
        $hot_map['g_is_index'] = 1;
        $hot_map['g_is_hot'] = 1;
        $hot_map['g_is_preferential'] = 0;
        $hot_map['g_is_recommend'] = 0;
        $hot_map['g_type'] = array('neq','promote');
        $hot_goods_list = $goods->where($hot_map)->field('g_id,g_name,g_sub_title,g_photo,g_price,vip_price,g_type,g_sales_volume')->order('g_rank asc')->select();
        $goods_hot['goods'] = $hot_goods_list;

        //限时特惠
        $positions = ['ap_flag' => 'indexnew-preferential'];
        $adPres = $adsPositions->where($positions)->find();
        $adPre = $adPres->adsToPositions(['ad_state' => 1])->toArray();
        $preferential_goods = $adPre[0];
        $preferential_map['g_on_sale'] = 1;
        $preferential_map['g_state'] = 1;
        $preferential_map['g_is_index'] = 1;
        $preferential_map['g_is_preferential'] = 1;
        $preferential_map['g_is_hot'] = 0;
        $preferential_map['g_is_recommend'] = 0;
        $preferential_map['g_type'] = array('neq','promote');
        $preferential_goods_list = $goods->where($preferential_map)->field('g_id,g_name,g_sub_title,g_photo,g_price,vip_price,g_type,g_sales_volume')->order('g_rank asc')->select();
        $preferential_goods['goods'] = $preferential_goods_list;

        $home = [
            'hot_goods' => $goods_hot,
            'preferential_goods' => $preferential_goods,
            'cate_goods' => $cate_list,
        ];

        return Utils::renderJson(compact('home'));
    }
}