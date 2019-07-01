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
        $malls::with(['relationsToPics'=>['img','sort']])->select();
    }

    
}