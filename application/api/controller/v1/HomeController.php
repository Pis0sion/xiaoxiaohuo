<?php


namespace app\api\controller\v1;

use app\api\repositories\HomeRepositories;
use app\common\model\AdsPositions;
use app\common\model\Goods;
use app\common\model\GoodsCategorys;

/**
 * Class HomeController
 * @package app\api\controller\v1
 */
class HomeController
{
    /**
     * @var HomeRepositories
     */
    protected $homeRepositories ;

    /**
     * HomeController constructor.
     * @param $homeRepositories
     */
    public function __construct(HomeRepositories $homeRepositories)
    {
        $this->homeRepositories = $homeRepositories;
    }

    /**
     * 首页轮播图
     * @param AdsPositions $adsPositions
     * @return array
     * @route("api/v1/banner","get")
     *
     */
    public function banners(AdsPositions $adsPositions)
    {
        return $this->homeRepositories->getBanner($adsPositions);
    }

    /**
     * 获取首页商品
     * @param GoodsCategorys $categorys
     * @param Goods $goods
     * @param AdsPositions $adsPositions
     * @return array
     * @route("api/v1/goods/home","get")
     *
     */
    public function products(GoodsCategorys $categorys,Goods $goods,AdsPositions $adsPositions)
    {
        return $this->homeRepositories->getProducts($categorys,$goods,$adsPositions);
    }

}