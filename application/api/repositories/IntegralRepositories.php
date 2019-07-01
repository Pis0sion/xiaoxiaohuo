<?php


namespace app\api\repositories;


class IntegralRepositories
{

    public function proList($malls)
    {
        return $malls::get(1);
    }
}