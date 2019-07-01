<?php


namespace app\api\repositories;


use app\api\utils\Utils;
use app\common\model\Users;
use app\common\validate\EntryValidate;
use app\common\validate\ForgetPwdValidate;
use app\common\validate\IsMobileValidate;
use app\common\validate\RegisterValidate;
use app\lib\enum\SmsEnum;
use app\lib\exception\ParameterException;
use think\Db;

/**
 * Class EntryRepositories
 * @package app\api\repositories
 */
class EntryRepositories
{
    /**
     * 登录
     * @param $request
     * @return array
     * @throws ParameterException
     */
    public function login($request)
    {
     //   try{
            // 验证层
            (new EntryValidate())->goCheck();
            // 接收参数
            $params = $request->only(['nickname','password']);
            // 判断用户是否存在
            $user = Users::userIsExists($params['nickname']);
            // 校检密码
            if(!$this->isMatch($params['password'],$user->u_password)) {
                throw new ParameterException();
            }
            // 生成token
            $token = app()->Utils->createToken($user);
            $uid = $user->id ;
            $u_agent = $user->uAgent ? 1 : 0 ;

//        }catch (\Throwable $e){
//            throw new ParameterException(['msg'=>'登录失败']);
//        }
//        // 返回结果
        return Utils::renderJson(compact('token','uid','u_agent'));
    }

    /**
     * 验证密码是否相等
     * @param $input_pwd
     * @param $db_pwd
     * @return bool
     */
    private function isMatch($input_pwd,$db_pwd)
    {
        return md5($input_pwd) == $db_pwd ;
    }

    /**
     * 注册
     * @param $request
     * @param $users
     * @param $smsLogs
     * @param \Closure $limits
     * @return array
     * @throws ParameterException
     */
    public function register($request,$users,$smsLogs,\Closure $limits)
    {
        (new RegisterValidate())->goCheck();
        //  每天限制5次
        $result = false ;
        $clientNo = $request->param('clientNo','-1');

        if($clientNo && ($clientNo != -1) )
        {
            $this->parentIsExists($request,$users);
        }

        if($limits($users)){
            return $this->registerToInsertIntoDataBase($request,$users,$smsLogs,function($users)use($request,$result){
                try{
                    $users->userIsExists($request->nickname);
                }catch (\Throwable $e){
                    $result = true ;
                }
                return $result ;
            },function ($smsLogs)use($request){
                $smsLogs->setUsedState($request->mobile,"register");
            });
        }
        throw new ParameterException(['msg'=>'超出当天注册限制']);
    }

    /**
     * 推荐人存不存在
     * @param $request
     * @param $users
     * @return mixed
     * @throws ParameterException
     */
    private function parentIsExists($request,$users)
    {
        try{
            return $users::getOrFail($request->clientNo);
        }catch (\Throwable $e){
            throw new ParameterException(['msg'=>'推荐用户不存在']);
        }
    }

    /**
     * 验证验证码
     * @param $request
     * @param $smsLogs
     * @return mixed
     */
    private function verifyCode($request,$smsLogs,$scene)
    {
        return $smsLogs->isValidCode($request->mobile, $request->smsCode, $scene);
    }

    /**
     * 注册入库
     * @param $request
     * @param $users
     * @param $smsLogs
     * @param \Closure $closure
     * @return array
     * @throws ParameterException
     */
    private function registerToInsertIntoDataBase($request,$users,$smsLogs,\Closure $userIsExists,\Closure $closure)
    {
        if($userIsExists($users))
        {
            if($this->verifyCode($request,$smsLogs,"register")){
                Db::startTrans();
                try{
                    $data = [
                        'u_phone' => $request->mobile,
                        'u_password' => md5($request->password),
                        'u_create_ip' => $request->ip(),
                        'u_nickname'=> $request->nickname ,
                        'u_parent_uid'=> $request->param("clientNo",0) ,
                    ];
                    $new_users = $users::create($data);
                    $new_users->addAccount();
                    $closure($smsLogs);
                    Db::commit();
                    return Utils::renderJson("注册成功");
                }catch (\Throwable $e){
                    Db::rollback();
                }
            }
        }
        throw new ParameterException(['msg'=>'用户名已存在']);
    }
    /**
     * 发送验证码
     * @param $request
     * @return mixed
     * @throws ParameterException
     */
    public function sendMsg($request)
    {
        (new IsMobileValidate())->goCheck();
        if(!array_key_exists($request->scene,SmsEnum::SMSTYPES)){
            throw new ParameterException(['msg' => '场景不存在']);
        }
        $params = [$request->mobile,$request->scene, $request->param("client","")] ;
        // 判断改手机号
        $isLegal = app()->ForbiddenToFrequent->init(...$params);
        return $isLegal->push(SmsEnum::SMSTYPES[$request->scene]);
    }

    /**
     * 忘记密码
     * @param $request
     * @param $users
     * @param $smsLogs
     * @param \Closure $closure
     * @return array
     * @throws ParameterException
     */
    public function forgetPwd($request,$users,$smsLogs,\Closure $closure)
    {
        // 验证
        try {
            (new ForgetPwdValidate())->goCheck();
            //确认手机号是否注册
            $user = Db::name("axgy_user")->where('u_nickname', $request->nickname)->whereIn('u_state', '1,2')
                ->order('u_create_time', 'desc')->find();
            if (empty($user)) {
                throw new ParameterException(['msg' => '用户不存在']);
            }
            if (trim($user['u_phone']) != trim($request->mobile)) {
                throw new ParameterException(['msg' => '请输入当前账户绑定的手机号！']);
            }
            //手机验证码是否正确

            if ($this->verifyCode($request, $smsLogs, 'forgetPwd')) {
                Db::startTrans();
                try {
                    $new = Users::get($user['id']);
                    $new->u_password = md5($request->password);
                    $new->save();
                    $closure($smsLogs);
                    Db::commit();
                    return Utils::renderJson("修改成功");
                } catch (\Throwable $e) {
                    Db::rollback();
                }
            }
            throw new ParameterException(['msg' => '修改失败']);
        } catch (\Throwable $e) {
            if ($e instanceof ParameterException) {
                throw new ParameterException(['msg' => $e->msg]);
            }
            throw new ParameterException(['msg' => '修改失败']);
        }
    }
}