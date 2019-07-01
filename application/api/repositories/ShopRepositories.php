<?php


namespace app\api\repositories;


use app\api\utils\Utils;
use app\common\model\Goods;
use app\lib\exception\ParameterException;
use think\Request;

/**
 * Class ShopRepositories
 * @package app\api\repositories
 */
class ShopRepositories
{
    /**
     * @param $categorys
     * @return array
     */
    public function getCate($categorys)
    {
        $cates = $categorys->where(['gc_state' => 1 ])->order("gc_rank asc")->field("gc_id,gc_name")->select();
        return Utils::renderJson($cates) ;
    }


    /**
     * @param $categorys
     * @return array
     * @throws ParameterException
     */
    public function getProducts($categorys)
    {
        if($categorys->isEmpty()) {
            throw new ParameterException(['msg' => '分类信息不存在']);
        }
        if($categorys->hasGoods()->getCurrentPage() ==1) {
            $data['total'] = $categorys->hasGoods()->total();
            $data['total_page'] = ceil($data['total']/10);
        }
        $data['data'] = $categorys->hasGoods()->getCollection();
        return Utils::renderJson($data);
    }

    /**
     * @param $request
     * @param $goods
     * @return \think\response\Json
     * @throws ParameterException
     */
    public function getProductToDetails($request,$goods)
    {
        $id = $request->param("goods_id");
        $from = $request->param("reqFrom","home");
        if (!intval($id)) {
            throw new ParameterException(['msg' => '参数错误']);
        }
        $goodsDetail = $goods->detail($id, $from);
        return json($goodsDetail);
    }


    public function getProductByKeyWords($request,$goodsModel)
    {
        $word = $request->param('keyword');
        if($word == ''){
            throw new ParameterException(['msg' => '请输入搜索内容！']);
        }
        $map = [
            'g_state' => 1,
            'g_on_sale'=> 1,
            'g_audit_state' =>1,
            'g_is_flash'=>0,
            'g_type'=>'normal'
        ];
        $map1 = [
            'g_state' => 1,
            'g_on_sale'=> 1,
            'g_audit_state' =>1,
            'g_is_flash'=>0,
            'g_type'=>'normal'
        ];

        $field = 'g_id,g_name,g_sub_title,g_photo,g_price,g_spec_format,g_type';
        $list = $goodsModel->whereLike('g_name','%'.$word.'%')->where($map)->field($field)->select();
        $list1 = $goodsModel->whereLike('keyword','%'.$word.'%')->where($map1)->field($field)->select();
        if($list1){
            if(is_array($list1->toArray()) && !empty($list1)){
                $list = $list->toArray() + $list1->toArray();
            }
        }
        return Utils::renderJson(compact('list'));
    }
}