<?php


namespace app\api\repositories;


use app\api\utils\Utils;
use app\common\model\{MultipleTypes, IntegralMalls};
use app\common\validate\OrdersValidate;
use app\common\validate\PlaceOrdersValidate;
use app\lib\exception\ParameterException;
use think\Db;

class IntegralRepositories
{
    /**
     * 商品列表
     * @param $malls
     * @return mixed
     */
    public function proList($malls)
    {
        $list = $malls->field('goods_id,goods_name,goods_price,goods_img,is_pay')->paginate(10);
        if($list->getCurrentPage() == 1) {
            $data['total'] = $list->total(); ;
            $data['total_page'] = ceil($data['total'] / 10);
        }
        $data['list'] = $list->getCollection() ;

        return Utils::renderJson($data);
    }

    /**
     * 获取商品详情
     * @param $malls
     * @param \Closure $isExist
     * @return array
     */
    public function proDetails($malls, \Closure $isExist)
    {
        $isExist($malls);
        return Utils::renderJson($this->renderDetails($malls));
    }

    /**
     * 格式化商品详情输出
     * @param $malls
     * @return mixed
     */
    private function renderDetails($malls)
    {
        return (new class($malls)
        {

            public $banners;

            public $price;

            public $goodName;

            public $views;

            public $is_pay;

            public $stock;

            public $thumb;

            public $goodDesc;

            /**
             *  constructor.
             * @param $malls
             */
            public function __construct($malls)
            {
                $this->banners = $malls->relationsToPics;
                $this->price = $malls->goods_price;
                $this->stock = $malls->goods_stock;
                $this->thumb = $malls->goods_thumb;
                $this->views = $malls->views;
                $this->is_pay = $malls->is_pay;
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
    public function prepareOrders($request, $malls, \Closure $isLegal, \Closure $isExist, \Closure $isEnough)
    {
        (new OrdersValidate())->goCheck();
        //  检测用户账户
        $isLegal(app()->usersInfo->uAccount);
        //  检测商品
        $isExist($malls);
        //  检测库存
        $isEnough($request->number, $malls->goods_stock);
        //  检测积分
        $multiple = MultipleTypes::where('id', $request->type)->findOrEmpty();
        //  检测分类合法
        $isLegal($multiple);
        //  实例化商品积分服务类
        $mode = app("Mode", [$request->number, $malls]);
        //  导入选择的积分分区
        $mode = $this->payCalIntegrals($multiple)->call($mode);
        //  判断是否满足
        if ($mode->isPayable(app()->usersInfo->uAccount->ua_integral_value)) {
            $list['desc'] = $mode->getDesc();
            $list['total_money'] = $mode->getTotalMoney();
            $list['deduct'] = $mode->getPayableIntegral();
            $list['integral'] = $mode->convertToIntegral();
            $list['freight'] = $mode->getFreight();
            $list['final_money'] = $mode->getPayMoney();

            return Utils::renderJson(compact('list'));
        }

        throw new ParameterException(['msg' => '积分不足']);
    }

    /**
     * 下单
     * @param $request
     * @param \Closure $isLegal
     * @param \Closure $isEnough
     * @return array
     * @throws ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function placeOrders($request, \Closure $isLegal, \Closure $isEnough)
    {
        (new PlaceOrdersValidate())->goCheck();
        // 获取地址
        $consigns = app()->usersInfo->hasUsersConsigns()->where('uc_id', $request->uc_id)->findOrEmpty();
        // 检测地址
        $isLegal($consigns);

        $multiple = MultipleTypes::where('id', $request->type)->findOrEmpty();
        //  检测分类合法
        $isLegal($multiple);
        //  实例化该商品
        $malls = IntegralMalls::where('goods_id', $request->goods_id)->findOrEmpty();
        //  检测商品
        $isLegal($malls);
        //  检测库存
        $isEnough($request->number, $malls->goods_stock);
        //  实例化商品积分服务类
        $mode = app("Mode", [$request->number, $malls]);
        //  导入选择的积分分区
        $mode = $this->payCalIntegrals($multiple)->call($mode);
        //  判断选择的积分是否满足
        if (!$mode->isPayable(app()->usersInfo->uAccount->ua_integral_value)) {
            throw new ParameterException(['msg' => '积分不足']);
        }
        Db::startTrans();
        try {
            $orders = [
                'order_sn' => Utils::makeResquestNo(),
                'goods_price' => $mode->getTotalMoney(),
                'shipping_fee' => $mode->getFreight(),
                'order_amount' => $mode->getPayMoney(),
                'order_integral' => $mode->convertToIntegral(),
                'pay_type' => 'alipay',
                'shipping_name' => $consigns->uc_consignee,
                'shipping_mobile' => $consigns->uc_phone,
                'shipping_addr' => $consigns->uc_province . $consigns->uc_city . $consigns->uc_county . $consigns->uc_location,
                'remark' => $request->param('remark',''),
            ];

            if ($order = app()->usersInfo->placeOrders($orders)) {
                $this->addRelationsGoods($malls, $request->number)->call($order);
                Db::commit();
                return Utils::renderJson(compact('order'));
            }
        } catch (\Throwable $e) {
            Db::rollback();
        }
        throw new ParameterException(['msg' => '下单失败']);
    }

    /**
     * 设置积分分区
     * @param $multiple
     * @return \Closure
     */
    private function payCalIntegrals($multiple)
    {
        return function () use ($multiple) {
            //  设置支付钱数
            $this->setPayMoney($multiple->tp_pay);
            //  设置描述
            $this->setDesc($multiple->tp_desc);
            //  设置兑换比例
            $this->setProportion($multiple->tp_proportion);

            return $this;
        };
    }

    /**
     * 关联订单商品新增
     * @param $malls
     * @param $num
     * @return \Closure
     */
    private function addRelationsGoods($malls, $num)
    {
        return function () use ($malls, $num) {
            //  订单关联商品
            $relations = [
                'order_sn' => $this->order_sn,
                'goods_id' => $malls->goods_id,
                'goods_name' => $malls->goods_name,
                'goods_img' => $malls->goods_img,
                'goods_price' => $malls->goods_price,
                'num' => $num,
            ];
            return $this->addRelationsToGoods($relations);
        };
    }



}