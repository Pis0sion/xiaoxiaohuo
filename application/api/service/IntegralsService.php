<?php
namespace app\api\service;

use app\common\model\Users;
use app\lib\enum\IntegralsMode;

/**
 * 做一些反射机制有关的 处理
 */
class IntegralsService {

    /**
     * @return array
     */
	public function integralsOfModes() {
	    return IntegralsMode::MODE ;
	}

    /**
     * @param $type
     * @param $supportedClass
     * @param array $params
     * @param bool $needInstance
     * @return bool|mixed|object
     * @throws \ReflectionException
     */
	public function initClass($type, $supportedClass, $params = [], $needInstance = true) {
		if(!array_key_exists($type, $supportedClass)) {
			return false;
		}

		$className = $supportedClass[$type];

		return $needInstance ? (new \ReflectionClass($className))->newInstanceArgs($params) : $className;
	}

    /**
     * @param $score
     * @param array $params
     * @return array
     * @throws \ReflectionException
     */
	public function getFitMode($score , ...$params)
    {
        $fits = [];

        $is_default = 1 ;

        foreach ($this->integralsOfModes() as $key => $value)
        {
            $classAttr = (new \ReflectionClass($value))->newInstanceArgs($params) ;

            if($classAttr->isPayable($score)){

                $fits[$key]['desc'] = $classAttr->getDesc() ;
                $fits[$key]['total_money'] = $classAttr->getTotalMoney() ;
                $fits[$key]['deduct'] = $classAttr->getPayableIntegral() ;
                $fits[$key]['integral'] = $classAttr->convertToIntegral() ;
                $fits[$key]['freight'] = $classAttr->getFreight() ;
                $fits[$key]['final_money'] = $classAttr->getPayMoney() ;
                $fits[$key]['is_default'] = $is_default ;
                $is_default = 0 ;
            }

        }
        return $fits ;
    }
}