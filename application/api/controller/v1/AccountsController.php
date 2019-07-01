<?php


namespace app\api\controller\v1;


use app\api\repositories\AccountsRepositories;
use app\common\model\BindBankCards;
use app\lib\exception\ParameterException;
use think\Request;

/**
 * 账户
 * Class AccountsController
 * @package app\api\controller\v1
 */
class AccountsController
{
    /**
     * @var AccountsRepositories
     */
    protected $accountsRepositories ;

    /**
     * AccountsController constructor.
     * @param $accountsRepositories
     */
    public function __construct(AccountsRepositories $accountsRepositories)
    {
        $this->accountsRepositories = $accountsRepositories;
    }

    /**
     * 提现
     * @param Request $request
     * @param BindBankCards $bindBankCards
     * @return array
     * @throws ParameterException
     * @route("api/v1/account/withdraw","post")
     * ->middleware('token')
     *
     */
    public function withdraw(Request $request,BindBankCards $bindBankCards)
    {
        return $this->accountsRepositories->addWithdraw($request,$bindBankCards,function($money){
            if ($money < 100) {
                throw new ParameterException(['msg' => "提现金额不能小于100！"]);
            }
            if ($money > 2000) {
                throw new ParameterException(['msg' => "提现金额单笔不大于2000元!"]);
            }
            if ($money % 100 != 0 ) {
                throw new ParameterException(['msg' => "提现金额必须为 100 的倍数!"]);
            }
        });
    }

}