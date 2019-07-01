<?php


namespace app\api\controller\v1;


use app\api\repositories\UpGradeRepositories;

class UpGradeController
{

    protected $upgrade ;

    /**
     * UpGradeController constructor.
     * @param $upgrade
     */
    public function __construct(UpGradeRepositories $upgrade)
    {
        $this->upgrade = $upgrade;
    }

    /**
     * 检测是否更新
     * @return array
     * @route("api/v1/upgrade","post")
     *
     */
    public function isCheckUpgrade()
    {
        return $this->upgrade->upgradeVersion();
    }


}