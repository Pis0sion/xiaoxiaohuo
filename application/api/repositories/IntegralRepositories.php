<?php


namespace app\api\repositories;


class IntegralRepositories
{
    /**
     * @param $malls
     * @return mixed
     */
    public function proList($malls)
    {
        return  $malls::with('relationsToPics')->find(1);
    }

}