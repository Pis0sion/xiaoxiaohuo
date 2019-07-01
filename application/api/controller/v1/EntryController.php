<?php


namespace app\api\controller\v1;

use app\api\repositories\EntryRepositories;
use app\common\model\SmsLogs;
use app\common\model\Users;
use app\lib\exception\ParameterException;
use think\Request;

/**
 * 入口
 * Class EntryController
 * @package app\api\controller
 */
class EntryController
{

    protected $entry ;

    /**
     * EntryController constructor.
     * @param EntryRepositories $entry
     */
    public function __construct(EntryRepositories $entry)
    {
        $this->entry = $entry ;
    }

    /**
     * 登陆操作
     * @param Request $request
     * @return array
     * @throws ParameterException
     * @route("api/v1/entry","post")
     *
     */
    public function userLogin(Request $request)
    {
        return $this->entry->login($request);
    }

    /**
     * 注册操作
     * @param Request $request
     * @param Users $users
     * @param SmsLogs $smsLogs
     * @return mixed
     * @throws ParameterException
     * @route("api/v1/register","post")
     *
     */
    public function register(Request $request,Users $users,SmsLogs $smsLogs)
    {
        return $this->entry->register($request,$users,$smsLogs,function ($users){
            return true ;
            //return  $users->getClientIpCounts() < 5 ;
        });
    }

    /**
     * 发送验证码
     * @param Request $request
     * @return mixed
     * @throws ParameterException
     * @route("api/v1/send/msg","post")
     *
     */
    public function sendMsgToOperate(Request $request)
    {
        return $this->entry->sendMsg($request);
    }

    /**
     * 忘记密码
     * @param Request $request
     * @param Users $users
     * @param SmsLogs $smsLogs
     * @return array
     * @throws ParameterException
     * @route("api/v1/forget/pwd","post")
     *
     */
    public function forgetToEntryPassword(Request $request,Users $users,SmsLogs $smsLogs)
    {
        return $this->entry->forgetPwd($request,$users,$smsLogs,function($smsLogs)use($request){
            $smsLogs->setUsedState($request->mobile,"forgetPwd");
        });
    }

}