<?php


namespace app\api\service;


/**
 * Class ProfitsToParents
 * @package app\api\service
 */
class ProfitsToParents
{
    /**
     * @var
     * 总额
     */
    protected $money ;
    /**
     * 直接推荐人
     * @var
     */
    protected $parent ;
    /**
     * 间接推荐人
     * @var
     */
    protected $indirect ;
    /**
     * @var
     * 直接抽成比例
     */
    protected $parent_rate ;
    /**
     * @var
     * 间接抽成比例
     */
    protected $indirect_rate ;


    /**
     * ProfitsToParents constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $money
     * @param $parent
     * @param $indirect
     * @return $this
     */
    public function init( $money, $parent = 0, $indirect = 0)
    {
        $this->money = $money;
        $this->parent = $parent;
        $this->indirect = $indirect;
        $this->parent_rate = config("profit.parent");
        $this->indirect_rate = config("profit.indirect");
        return $this ;
    }

    /**
     * @param mixed $parent_rate
     */
    public function setParentRate($parent_rate): void
    {
        $this->parent_rate = $parent_rate;
    }

    /**
     * @param mixed $indirect_rate
     */
    public function setIndirectRate($indirect_rate): void
    {
        $this->indirect_rate = $indirect_rate;
    }

    /**
     * @return array
     */
    public function handler()
    {
        $profits = [];

        $this->operateProfits($this->parent,$this->parent_rate,function($uid,$rate)use(&$profits){
             $this->CalProfits($profits,$uid,$rate,"direct");
        });

        $this->operateProfits($this->indirect,$this->indirect_rate,function($uid,$rate)use(&$profits){
             $this->CalProfits($profits,$uid,$rate,"indirect");
        });

        return $profits ;
    }

    /**
     * @param $uid
     * @param $rate
     * @param \Closure $closure
     * @return mixed
     */
    private function operateProfits($uid,$rate,\Closure $closure)
    {
        return $closure($uid,$rate);
    }

    /**
     * @param $profits
     * @param $uid
     * @param $rate
     * @param $type
     * @return mixed
     */
    private function CalProfits(&$profits,$uid,$rate,$type)
    {
        if(!empty($uid) && ($rate != 0)) {
            $data = [
                'uid' => $uid ,
                'profit' => bcmul($this->money,$rate,2) ,
                'type' => $type ,
                'rate' => $rate ,
            ];
            $profits[] = $data ;
        }
    }

}