<?php


namespace app\api\controller\v1;


use app\api\repositories\UsersRepositories;
use app\api\utils\Utils;
use app\common\model\Banks;
use app\common\model\BindBankCards;
use app\common\model\Regions;
use app\common\model\SmsLogs;
use app\common\model\UserConsigns;
use app\lib\enum\IUrls;
use app\lib\exception\ParameterException;
use GuzzleHttp\Client;
use think\exception\DbException;
use think\Request;

/**
 * 用户
 * Class UsersController
 * @package app\api\controller\v1
 */
class UsersController
{
    /**
     * @var UsersRepositories
     */
    protected $usersRepositories ;

    /**
     * UsersController constructor.
     * @param $usersRepositories
     */
    public function __construct(UsersRepositories $usersRepositories)
    {
        $this->usersRepositories = $usersRepositories;
    }

    /**
     * 获取用户信息
     * @return array
     * @route("api/v1/users/info","post")
     * ->middleware('token')
     *
     */
    public function getUsersInformations()
    {
        return $this->usersRepositories->getUsers();
    }
    /**
     * 获取客服电话
     * @return array
     * @route("api/v1/sys/link","post")
     *
     */
    public function getLinker()
    {
        $link = "4006157558";
        return Utils::renderJson(compact('link'));
    }

    /**
     * 上传头像
     * @param Request $request
     * @return array
     * @throws ParameterException
     * @route("api/v1/portrait","post")
     * ->middleware('token')
     *
     */
    public function uploadToUserHeadPortrait(Request $request)
    {
        return $this->usersRepositories->receiveFiles($request,function ($path){
            @unlink($path);
        });
    }

    /**
     * 修改昵称
     * @param Request $request
     * @return array
     * @throws ParameterException
     * @route("api/v1/alter/profile","post")
     * ->middleware('token')
     *
     */
    public function alterToUsersNickName(Request $request)
    {
        return $this->usersRepositories->alterUsersNickName($request);
    }

    /**
     * 提交实名认证
     * @param Request $request
     * @param SmsLogs $smsLogs
     * @return array
     * @throws ParameterException
     * @route("api/v1/isreal/users","post")
     * ->middleware('token')
     *
     */
    public function usersToCertification(Request $request,SmsLogs $smsLogs)
    {
        return $this->usersRepositories->certification($request,$smsLogs,function($smsLogs)use($request){
            $smsLogs->setUsedState($request->mobile,"certification");
        });
    }

    /**
     * 获取用户实名认证信息
     * @return array
     * @route("api/v1/is/real","post")
     * ->middleware('token')
     *
     */
    public function getUsersToCertifications()
    {
        //  TODO  审核
        return $this->usersRepositories->getCertifications();
    }

    /**
     * 绑定银行卡
     * @param Request $request
     * @param BindBankCards $bindBankCards
     * @param SmsLogs $smsLogs
     * @param Regions $regions
     * @param Banks $banks
     * @param Client $client
     * @return array
     * @throws ParameterException
     * @route("api/v1/bind/bank","post")
     * ->middleware('token')
     *
     */
    public function userToBindBankCard(Request $request,BindBankCards $bindBankCards,SmsLogs $smsLogs,Regions $regions,Banks $banks,Client $client)
    {
        return $this->usersRepositories->bindBankCard($request,$bindBankCards,$smsLogs,$regions,$banks,function($cardNum)use($client){
             return ($this->verifyCardNum($cardNum,$client))();
        });
    }

    /**
     * 用户绑定银行卡
     * @return array
     * @route("api/v1/banks/user","post")
     * ->middleware('token')
     *
     */
    public function userToBindBanks()
    {
        return $this->usersRepositories->bankLists();
    }

    /**
     * 解绑银行卡
     * @param BindBankCards $bindBankCards
     * @return array
     * @throws ParameterException
     * @route("api/v1/del/:ubc_id/bank","post")
     * ->model('ubc_id','\app\common\model\BindBankCards',false)
     * ->middleware('token')
     *
     */
    public function userToDelBanks(BindBankCards $bindBankCards)
    {
        return $this->usersRepositories->delBanks($bindBankCards,function ($bindBankCards){
            $this->strategy($bindBankCards);
        });
    }

    /**
     * 获取默认的银行卡
     * @return array
     * @route("api/v1/get/default/bank","post")
     * ->middleware('token')
     *
     */
    public function getUsersToDefaultBanks(Request $request)
    {
        return $this->usersRepositories->getUsersToDefaultBanks($request);
    }


    /**
     * 设置默认的银行卡
     * @param BindBankCards $bindBankCards
     * @return array
     * @throws ParameterException
     * @route("api/v1/default/:ubc_id/bank","post")
     * ->model('ubc_id','\app\common\model\BindBankCards',false)
     * ->middleware('token')
     *
     */
    public function userToSetDefaultBanks(BindBankCards $bindBankCards)
    {
        return $this->usersRepositories->setBanksToDefault($bindBankCards,function($bindBankCards){
            $this->strategy($bindBankCards);
        });
    }

    /**
     * 获取地址列表
     * @return array
     * @route("api/v1/consigns/list","post")
     * ->middleware('token')
     *
     */
    public function getConsignsList()
    {
        return $this->usersRepositories->getAllConsigns();
    }

    /**
     * 获取用户的收货地址
     * @param Request $request
     * @return array
     * @route("api/v1/consigns/default","post")
     * ->middleware('token')
     *
     */
    public function getDefaultConsigns(Request $request)
    {
        return $this->usersRepositories->getDefaultConsigns($request);
    }

    /**
     * 添加地址
     * @param Request $request
     * @param Regions $regions
     * @return array
     * @throws ParameterException
     * @route("api/v1/add/consign","post")
     * ->middleware('token')
     *
     */
    public function addConsigns(Request $request,Regions $regions)
    {
        return $this->usersRepositories->addUsersConsigns($request,$regions);
    }

    /**
     * 设置默认地址
     * @param UserConsigns $consigns
     * @return array
     * @throws ParameterException
     * @route("api/v1/default/:uc_id/consign","post")
     * ->model('uc_id','\app\common\model\UserConsigns',false)
     * ->middleware('token')
     *
     */
    public function setDefaultConsigns(UserConsigns $consigns)
    {
        return $this->usersRepositories->setConsignsToDefault($consigns,function($consigns){
            $this->strategy($consigns);
        });
    }

    /**
     * 编辑用户地址
     * @param Request $request
     * @param UserConsigns $consigns
     * @return array
     * @throws ParameterException
     * @route("api/v1/edit/:uc_id/consign","post")
     * ->model('uc_id','\app\common\model\UserConsigns',false)
     * ->middleware('token')
     *
     */
    public function editUserConsigns(Request $request , UserConsigns $consigns)
    {
        return $this->usersRepositories->editConsignsToDefault($request,$consigns,function($consigns){
            $this->strategy($consigns);
        });
    }

    //  删除用户地址
    public function delUserConsigns()
    {

    }

    /**
     * 查询团队
     * @param Request $request
     * @return array
     * @throws DbException
     * @throws ParameterException
     * @route("api/v1/team","post")
     * ->middleware('token')
     *
     */
    public function getDirectUsers(Request $request)
    {
        return $this->usersRepositories->getUsesToDirect($request);
    }

    /**
     * 结算货款
     * @param Request $request
     * @return array
     * @throws ParameterException
     * @route("api/v1/getUserProfitLog","post")
     * ->middleware('token')
     *
     */
    public function getUserProfitLog(Request $request)
    {
        return $this->usersRepositories->getUserProfitLog($request);
    }

    /**
     * 积分列表
     * @return array
     * @route("api/v1/getUseruaIntegral","post")
     * ->middleware('token')
     *
     */
    public function getUseruaIntegral()
    {
        return $this->usersRepositories->getUseruaIntegrals();
    }

    /**
     * 团队奖励
     * @return array
     * @route("api/v1/getTeamProfits","post")
     * ->middleware('token')
     *
     */
    public function getTeamProfits()
    {
        return $this->usersRepositories->getTeamProfits();
    }


    /**
     * 提现用户的记录
     * @return array
     * @route("api/v1/withdraw/list","post")
     * ->middleware('token')
     *
     */
    public function getWithdrawList()
    {
        return $this->usersRepositories->withdrawList();
    }

    /**
     * 获取银行卡列表
     * @return array
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @route("api/v1/bank","post")
     *
     */
    public function getUserBankLists()
    {
        return $this->usersRepositories->bankList();
    }

    /**
     * 策略
     * @param $bindBankCards
     * @return bool
     * @throws ParameterException
     */
    private function strategy($bindBankCards)
    {
        if($bindBankCards->uid != app()->usersInfo->id) {
            throw new ParameterException(['msg' => '无权限操作']);
        }
        return true ;
    }

    /**
     * @param $cardNum
     * @param $client
     * @return callable
     */
    public function verifyCardNum($cardNum,$client)
    {
        return new class($cardNum,$client){
            /**
             * @var
             */
            public $cardNum ;
            /**
             * 请求客户端
             * @var
             */
            public $client ;

            /**
             *  constructor.
             * @param $cardNum
             */
            public function __construct($cardNum,$client)
            {
                $this->cardNum = $cardNum;
                $this->client = $client;
            }

            /**
             * @return mixed
             */
            public function __invoke()
            {
                // TODO: Implement __invoke() method.
                return json_decode($this->client->request("POST",sprintf(IUrls::IDCARD,$this->cardNum))->getBody()->getContents());
            }
        };
    }


}