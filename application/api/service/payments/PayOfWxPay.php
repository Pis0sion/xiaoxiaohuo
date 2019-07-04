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

    /**
     * 处理回调
     * @param \Closure $success
     * @param \Closure $fail
     */
    public function doNotify(\Closure $success,\Closure $fail)
    {
        $request = $this->gateWay->completePurchase();
        $request->setParams($request()->post()); //Optional

        try {
            /** @var \Omnipay\Alipay\Responses\AopCompletePurchaseResponse $response */
            $response = $request->send();

            if($response->isPaid()){
                /**
                 * Payment is successful
                 */
                $success();
                die('success'); //The response should be 'success' only
            }else{
                /**
                 * Payment is not successful
                 */
                $fail();
                die('fail');
            }
        } catch (\Throwable $e) {
            /**
             * Payment is not successful
             */
            die('fail');
        }
    }
}