<?php


namespace app\api\controller\v1;


use app\api\repositories\ShopRepositories;
use app\common\model\Goods;
use app\common\model\GoodsCategorys;
use think\Request;

/**
 * Class ShopController
 * @package app\api\controller\v1
 */
class ShopController
{
    /**
     * @var
     */
    protected $shopRepositories ;

    /**
     * ShopController constructor.
     * @param $shopRepositories
     */
    public function __construct(ShopRepositories $shopRepositories)
    {
        $this->shopRepositories = $shopRepositories;
    }

    /**
     * 获取分类
     * @param GoodsCategorys $categorys
     * @return mixed
     * @route("api/v1/cates","post")
     *
     */
    public function category(GoodsCategorys $categorys)
    {
        return $this->shopRepositories->getCate($categorys);
    }

    /**
     * 根据分类找商品
     * @param GoodsCategorys $categorys
     * @return array
     * @throws \app\lib\exception\ParameterException
     * @route("api/v1/pros/:gc_id/cate","post")
     * ->model('gc_id','\app\common\model\GoodsCategorys',false)
     *
     */
    public function getProductsByCategory(GoodsCategorys $categorys)
    {
        return $this->shopRepositories->getProducts($categorys);
    }

    /**
     * 商品详情
     * @param Request $request
     * @param Goods $goods
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @route("api/v1/details/product","post")
     *
     */
    public function getProductToDetails(Request $request,Goods $goods)
    {
        return$this->shopRepositories->getProductToDetails($request,$goods);
    }

    /**
     * 商品搜索
     * @param Request $request
     * @param Goods $goods
     * @return array
     * @throws \app\lib\exception\ParameterException
     * @route("api/v1/search/product","post")
     *
     */
    public function getProductByKeyWords(Request $request,Goods $goods)
    {
        return$this->shopRepositories->getProductByKeyWords($request,$goods);
    }
}