<?php


namespace app\api\controller\v1;

use app\api\repositories\IntegralRepositories;
use app\common\model\IntegralMalls;

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
     * @param IntegralMalls $malls
     * @return mixed
     * @route("api/v1/pro/integral/list","get")
     *
     */
    public function getListByProducts(IntegralMalls $malls)
    {
        return $this->integral->proList($malls);
    }



}