<?php


namespace app\api\traits;


use app\lib\exception\ParameterException;

trait Regions
{

    public function obtainRegionsByCountyId($regions,$county_id)
    {
        $countyRes = $regions->isExist($county_id);
        if (!$countyRes) {
            throw new ParameterException(['msg' => "请正确选择开户行区、县"]);
        }

        $cityRes = $regions->isExist($countyRes->parentid);
        if (!$cityRes) {
            throw new ParameterException(['msg' => "请正确选择开户行城市"]);
        }

        $provinceRes = $regions->isExist($cityRes->parentid);
        if (!$provinceRes) {
            throw new ParameterException(['msg' => "请正确选择开户行省份"]);
        }

        return compact('countyRes','cityRes','provinceRes');
    }

}