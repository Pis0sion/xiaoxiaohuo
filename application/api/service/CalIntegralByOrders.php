<?php


namespace app\api\service;

/**
 * Class CalIntegralByOrders
 * @package app\api\service
 */
class CalIntegralByOrders
{
    /**
     * @var
     * 个数
     */
    protected $count ;
    /**
     * @var
     * 商品
     */
    protected $product ;
    /**
     * @var
     * 运费
     */
    protected $freight = 0.00 ;

    /**
     * @param mixed $freight
     */
    public function setFreight($freight): void
    {
        $this->freight = $freight;
    }

    /**
     * CalIntegralByOrders constructor.
     * @param $count
     * @param $product
     */
    public function __construct($count, $product)
    {
        $this->count = $count;
        $this->product = $product;
    }

    /**
     * 总金额
     * @return string
     */
    protected function getTotalMoney()
    {
        return bcmul($this->product->goods_price,$this->count,2);
    }

    /**
     * 抵扣积分
     * @return string
     */
    protected function getTotalIntegral()
    {
        return bcmul($this->product->goods_integral,$this->count,2);
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.
        $totalMoney = $this->getTotalMoney() ;

        $integrals = $this->getTotalIntegral() ;

        return new class($totalMoney,$this->freight,$integrals){
            /**
             * @var int
             * 总额
             */
            public $totalMoney = 0 ;
            /**
             * @var int
             * 运费
             */
            public $freight = 0 ;
            /**
             * @var
             * 积分折扣
             */
            public $discount ;

            /**
             *  constructor.
             * @param int $totalMoney
             * @param int $freight
             * @param $discount
             */
            public function __construct($totalMoney, $freight, $discount)
            {
                $this->totalMoney = $totalMoney;
                $this->freight = $freight;
                $this->discount = $discount;
            }



        };
    }

}