<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/5
 * Time: 9:59
 */

namespace app\http\middleware;


use app\common\model\Users;
use app\lib\exception\TokenException;

class TokenMiddleWare
{
    /**
     * 执行入口
     * @param $request
     * @param \Closure $next
     * @return mixed
     * @throws TokenException
     */
    public function handle($request,\Closure $next)
    {
        try {
            // 接受token
            $token = $request->header('token') ;
            // 解析token
            $hash = app()->Utils->parseToTokens($token);
            // 获取操作的用户
            $user_id = $hash['data'] ;
            // 实例化用户对象
            $users= Users::findOrEmpty($user_id,'uAgent,uAccount,hasParents');

            // 如果为空
            if($users->isEmpty())
            {
                throw new TokenException();
            }

        }catch (\Throwable $e)
        {
            throw new TokenException();
        }
        //  绑定用户到容器上
        bind("usersInfo",$users);
        //  执行请求
        return $next($request);
    }
}