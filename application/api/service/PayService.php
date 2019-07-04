<?php
namespace app\api\service;


use app\lib\enum\PayChannels;
use app\lib\exception\ParameterException;

/**
 * 做一些反射机制有关的 处理
 */
class PayService {

    /**
     * 支持的通道
     * @return array
     */
	public function supportedClass() {
	    return PayChannels::CHANNELS ;
	}

    /**
     * 初始化支付通道
     * @param $type
     * @param $supportedClass
     * @param mixed ...$params
     * @return object
     * @throws ParameterException
     * @throws \ReflectionException
     */
	public function initClass($type, $supportedClass, ... $params) {
		if(!array_key_exists($type, $supportedClass)) {
			throw new ParameterException(['msg' => '暂时不支持该通道']);
		}
		$className = $supportedClass[$type];
		return  (new \ReflectionClass($className))->newInstanceArgs($params) ;
	}

    /**
     * 支付
     * @param $type
     * @param $order
     * @return mixed
     * @throws ParameterException
     * @throws \ReflectionException
     */
	public function payAction($type,$order)
    {
        $payChannel = app("PayService",[$this->initClass($type,$this->supportedClass())]);
        return $payChannel->purchase($order);
    }

    /**
     * 处理回调
     * @param \Closure $success
     * @param \Closure $fail
     * @return mixed
     * @throws ParameterException
     * @throws \ReflectionException
     */
    public function payNotify(\Closure $success,\Closure $fail)
    {
        $payChannel = app("PayService",[$this->initClass("alipay",$this->supportedClass())]);
        return $payChannel->doNotify($success,$fail);
    }

}