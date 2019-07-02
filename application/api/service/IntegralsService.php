<?php
namespace app\api\service;

use app\common\model\Users;

/**
 * 做一些反射机制有关的 处理
 */
class IntegralsService {

    /**
     * @return array
     */
	public function integralsOfModes() {
		return [
			"one"   => \app\api\service\integrals\IntegralOfModeOne::class,
			"two"   => \app\api\service\integrals\IntegralOfModeTwo::class,
			"three" => \app\api\service\integrals\IntegralOfModeThree::class,
		];
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

        foreach ($this->integralsOfModes() as $key => $value)
        {
            $classAttr = (new \ReflectionClass($value))->newInstanceArgs(...$params) ;

            if($classAttr->isPayable($score)){

                $fits[$key]['desc'] = $classAttr->getDesc() ;
            }

        }
        return $fits ;
    }
}