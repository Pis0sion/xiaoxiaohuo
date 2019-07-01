<?php
/**
 * Created by 七月.
 * User: 七月
 * Date: 2017/2/14 我去，情人节，886214
 * Time: 1:03
 */

namespace app\lib\exception;

/**
 * token验证失败时抛出此异常 
 */
class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg = 'Missing configuration parameters!';
    public $errorCode = 10001;
}