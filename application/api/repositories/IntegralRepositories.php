<?php


namespace app\api\repositories;


class IntegralRepositories
{

    public function proList($malls)
    {
        $mall = $malls::with('relationsToPics')->find(1);

        return $mall->relationsToPics ;
    }
}