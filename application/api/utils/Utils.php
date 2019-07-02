<?php


namespace app\api\utils;


use app\api\service\QueuesService;
use app\api\service\TokensService;

class Utils
{

    /**
     * 生成订单号
     * @return string
     */
    public static function makeResquestNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2019] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    /**
     * 生成token
     * @param $user
     * @return string
     */
    public function createToken($user)
    {
        $payload = $this->prepareToCreateToken($user);
        $tokens = TokensService::init($payload);
        return $tokens->createToken();
    }

    /**
     * token生成规则
     * @param $user
     * @return mixed
     */
    private function prepareToCreateToken($user)
    {
        return (new class($user){
            public $iss ;
            public $iat ;
            public $nbf ;
            public $exp ;
            public $data ;
            /**
             *  constructor.
             * @param $user
             */
            public function __construct($user)
            {
                $this->iss = config('tokens.iss');
                $this->iat = time();
                $this->nbf = time();
                $this->exp = time()+60*60*24*7;
                $this->data = $user->id;
            }

        });
    }

    /**
     * 解密token
     * @param $token
     * @return array
     */
    public function parseToTokens($token):array
    {
       return  (array)TokensService::init()->parseToken($token);
    }

    /**
     * 成功统一返回
     * @param $msg
     * @param string $error_code
     * @param string $request_url
     * @param int $code
     * @return array
     */
    public static function renderJson($msg,$error_code = "0000",$request_url = "",$code = 0)
    {
        return compact('msg','error_code','request_url','code');
    }

    /**
     * 获取activemq队列
     * @return QueuesService|array
     */
    public function activeMq()
    {
        return QueuesService::getInstance();
    }

}