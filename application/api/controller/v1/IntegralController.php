<?php


namespace app\api\controller\v1;

use app\api\repositories\IntegralRepositories;

/**
 * 积分商城模块api
 * Class IntegralController
 * @package app\api\controller\v1
 */
class IntegralController
{
    /**
     * @var IntegralRepositories
     */
    protected  $integral ;

    /**
     * IntegralController constructor.
     * @param $integral
     */
    public function __construct(IntegralRepositories $integral)
    {
        $this->integral = $integral;
    }

    /**
     * @route("api/v1/pro/integral/list","post")
     *
     */
    public function getListByProducts()
    {
        return $this->integral->proList();
    }



}