<?php


namespace app\api\service;


class AgentsLevelService
{
    /**
     * 总钱数
     * @var
     */
    protected $profits ;

    /**
     * AgentsLevelService constructor.
     */
    public function __construct()
    {
    }

    /**
     *  初始化
     * @param $profits
     * @return $this
     */
    public function init($profits)
    {
        $this->profits = $profits;
        $this->grades = config('grades.');
        return $this ;
    }

    /**
     * 获取等级
     * @return mixed
     */
    public function getLevels()
    {
        return $this->doLogic(function ($pixel){
            if(($pixel >= 1) && ($pixel < 10)){
                return $this->grades['junior'];
            }
            if($pixel >= 10){
                return $this->grades['medium'];
            }
        });
    }

    /**
     * @param \Closure $level
     * @return mixed
     */
    private function doLogic(\Closure $level)
    {
        //  默认等级
        $myGrade = $this->grades['default'];
        //  获取需要的倍数
        $pixel = floor($this->profits/config('profit.base'));
        //  判断
        if($pixel == 0) {
            return $myGrade ;
        }
        return $level($pixel) ;
    }

}