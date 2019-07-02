<?php
namespace app\api\service;

use app\lib\enum\IntegralsMode;

/**
 * 做一些反射机制有关的 处理
 */
class IntegralsService {

    /**
     * @return array
     */
	public function supportedClass() {

	}

    /**
     * @param $type
     * @param $supportedClass
     * @param mixed ...$params
     * @return bool|object
     * @throws \ReflectionException
     */
	public function initClass($type, $supportedClass, ... $params) {
		if(!array_key_exists($type, $supportedClass)) {
			return false;
		}

		$className = $supportedClass[$type];

		return  (new \ReflectionClass($className))->newInstanceArgs($params) ;
	}

}