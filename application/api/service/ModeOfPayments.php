<?php


namespace app\api\service;


abstract class ModeOfPayments
{
    /**
     * @var
     *  几园区
     */
    protected $payMoney ;

    /**
     * @var
     * 商品个数
     */
    protected $count ;
    /**
     * @var
     * 商品
     */
    protected $product ;
    /**
     * @var
     * 比例    money / integral
     */
    protected $proportion = 1.00;
    /**
     * @var
     * 描述
     */
    protected $desc ;
    /**
     * @var float
     * 运费
     */
    protected $freight = 0.00 ;

    /**
     * ModeOfPayments constructor.
     * @param $count
     * @param $product
     */
    public function __construct($count, $product)
    {
        $this->count = $count;
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count): void
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product): void
    {
        $this->product = $product;
    }

    /**
     * @param mixed $proportion
     */
    public function setProportion($proportion): void
    {
        $this->proportion = $proportion;
    }

    /**
     * @return mixed
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param mixed $desc
     */
    public function setDesc($desc): void
    {
        $this->desc = $desc;
    }

    /**
     * @return mixed
     */
    public function getPayMoney()
    {
        return $this->payMoney;
    }

    /**
     * @return float
     */
    public function getFreight(): float
    {
        return $this->freight;
    }

    /**
     * @param float $freight
     */
    public function setFreight(float $freight): void
    {
        $this->freight = $freight;
    }

    /**
     * 总额
     * @return string
     */
    public function getTotalMoney()
    {
        return bcmul($this->product->goods_price,$this->count,2);
    }

    /**
     * 应付的金额
     * @return string
     */
    public function getPayableIntegral()
    {
        return bcsub($this->getTotalMoney(),$this->payMoney,2);
    }

    /**
     * 转换成应付的积分
     * @return string|null
     */
    public function convertToIntegral()
    {
        return bcdiv($this->getPayableIntegral(),$this->proportion,2);
    }

    /**
     * 是否能够支付
     * @param $score
     * @return bool
     */
    public function isPayable($score)
    {
        return $score >= $this->convertToIntegral();
    }


}