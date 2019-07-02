<?php


namespace app\api\service\payments;


class PayOfWxPay extends IPayChannels
{
    /**
     * 初始化设置
     * @return \Omnipay\Common\GatewayInterface
     */
    public function init()
    {
        $gateWay = Omnipay::create('Alipay_AopF2F');
        $gateWay->setSignType('RSA2');
        $gateWay->setAppId('the_app_id');
        $gateWay->setPrivateKey('path/to/the_app_private_key');
        $gateWay->setAlipayPublicKey('path/to/the_alipay_public_key');
        $gateWay->setNotifyUrl('https://www.example.com/notify');
        return $gateWay ;
    }

    /**
     * @param  $orders
     * @return mixed
     */
    public function payOrder($orders)
    {
        // TODO: Implement payOrder() method.
        $request = $this->gateWay->purchase();
        $request->setBizContent([
            'subject'      => $orders->goods_name,
            'out_trade_no' => $orders->order_sn,
            'total_amount' => $orders->order_amount,
        ]);
        $response = $request->send();
        return $response->getQrCode();
    }
}