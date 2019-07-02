<?php


namespace app\api\repositories;


use app\api\utils\Utils;
use app\common\model\{MultipleTypes,IntegralMalls};
use app\common\validate\OrdersValidate;
use app\lib\exception\ParameterException;

class IntegralRepositories
{
    /**
     * @param $malls
     * @return mixed
     */
    public function proList($malls)
    {
        $list = $malls->field('goods_id,goods_name,goods_price,goods_integral,goods_img')->paginate(10);
        return Utils::renderJson($list);
    }

    /**
     * 获取商品详情
     * @param $malls
     * @param \Closure $isExist
     * @return array
     */
    public function proDetails($malls,\Closure $isExist)
    {
        $isExist($malls);
        return Utils::renderJson($this->renderDetails($malls));
    }

    private function renderDetails($malls)
    {
        return (new class($malls){

            public $banners ;

            public $price ;

            public $goodName ;

            public $goodDesc ;

            /**
             *  constructor.
             * @param $malls
             */
            public function __construct($malls)
            {
                $this->banners = $malls->relationsToPics;
                $this->price = $malls->goods_price;
                $this->goodName = $malls->goods_name;
                $this->goodDesc = $malls->goods_desc;
            }

        });
    }

    /**
     * 获取支付分类
     * @return array
     */
    public function getMultiples()
    {
        return Utils::renderJson(MultipleTypes::all());
    }

    /**
     * 预下单
     * @param $request
     * @param $malls
     * @param \Closure $isLegal
     * @param \Closure $isExist
     * @param \Closure $isEnough
     * @return array
     * @throws ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function prepareOrders($request,$malls,\Closure $isLegal,\Closure $isExist,\Closure $isEnough)
    {
        (new OrdersValidate())->goCheck();
        //  检测用户账户
        $isLegal(app()->usersInfo->uAccount);
        //  检测商品
        $isExist($malls);
        //  检测库存
        $isEnough($request->number,$malls->goods_stock);
        //  检测积分
        $multiple = MultipleTypes::where('id',$request->type)->findOrEmpty();
        //  检测分类合法
        $isLegal($multiple);
        $mode = app("Mode",[$request->number,$malls]);
        //  设置支付钱数
        $mode->setPayMoney($multiple->tp_pay);
        //  设置描述
        $mode->setDesc($multiple->tp_desc);
        //  设置兑换比例
        $mode->setProportion($multiple->tp_proportion);

        if($mode->isPayable(app()->usersInfo->uAccount->ua_integral_value))
        {
            $list['desc'] = $mode->getDesc() ;
            $list['total_money'] = $mode->getTotalMoney() ;
            $list['deduct'] = $mode->getPayableIntegral() ;
            $list['integral'] = $mode->convertToIntegral() ;
            $list['freight'] = $mode->getFreight() ;
            $list['final_money'] = $mode->getPayMoney() ;

            return Utils::renderJson(compact('list'));
        }

        throw new ParameterException(['msg' => '积分不足']);
    }

    // 下单
    public function placeOrders($request,\Closure $isLegal,\Closure $isEnough)
    {
        // 首先获取地址
        $consigns = app()->usersInfo->hasUsersConsigns()->where('uc_id',$request->uc_id)->findOrEmpty();
        // 检测地址
        $isLegal($consigns);

        $multiple = MultipleTypes::where('id',$request->type)->findOrEmpty();
        //  检测分类合法
        $isLegal($multiple);

        $malls = IntegralMalls::where('goods_id',$request->goods_id)->findOrEmpty();
        //  检测商品
        $isLegal($malls);
        //  检测库存
        $isEnough($request->number,$malls->goods_stock);

        $mode = app("Mode",[$request->number,$malls]);
        //  设置支付钱数
        $mode->setPayMoney($multiple->tp_pay);
        //  设置描述
        $mode->setDesc($multiple->tp_desc);
        //  设置兑换比例
        $mode->setProportion($multiple->tp_proportion);

        if(!$mode->isPayable(app()->usersInfo->uAccount->ua_integral_value))
        {
            throw new ParameterException(['msg' => '积分不足']);
        }

        $orders = [

            'order_sn'  =>  Utils::makeResquestNo() ,
            'goods_price'  =>  $mode->getTotalMoney() ,
            'shipping_fee'  =>  $mode->getFreight() ,
            'order_amount'  =>  $mode->getPayMoney() ,
            'pay_type'  =>  'alipay' ,
            'shipping_name'  =>  $consigns->uc_consignee ,
            'shipping_mobile'  =>  $consigns->uc_phone ,
            'shipping_addr'  => $consigns->uc_province.$consigns->uc_city.$consigns->uc_county.$consigns->uc_location,
            'remark'  =>  $request->remark ,

        ];

        if(app()->usersInfo->placeOrders($orders)) {
            return Utils::renderJson("下单成功");
        }

        throw new ParameterException(['msg' => '下单失败']);


    }




}