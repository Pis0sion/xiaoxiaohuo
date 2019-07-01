<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/5
 * Time: 10:03
 */

namespace app\api\service;


use Firebase\JWT\JWT;

class TokensService
{
    /**
     * 盐值
     * @var
     */
    protected $salt ;
    /**
     * 加密实体
     * @var
     */
    protected $payload ;

    /**
     * TokensService constructor.
     * @param $salt
     * @param $payload
     */
    public function __construct($salt, $payload)
    {
        $this->salt = $salt;
        $this->payload = $payload;
    }

    /**
     * @param $payload
     * @return TokensService
     */
    public static function init($payload = "")
    {
        $salt = config("tokens.key");
        return new self($salt,$payload);
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * 生成token
     * @return string
     */
    public function createToken()
    {
        return JWT::encode($this->payload,$this->salt);
    }

    /**
     * 解析token
     * @param $token
     * @return object
     */
    public function parseToken($token)
    {
        return JWT::decode($token,$this->salt,['HS256']);
    }

}