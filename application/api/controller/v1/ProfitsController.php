<?php


namespace app\api\controller\v1;


use app\api\repositories\ProfitsRepositories;
use app\common\model\Accounts;
use app\common\model\ProfitLogs;
use app\lib\exception\ParameterException;
use think\Request;

/**
 * 分润
 * Class ProfitsController
 * @package app\api\controller\v1
 */
class ProfitsController
{
    /**
     * @var ProfitsRepositories
     */
    protected $profitRepositories ;

    /**
     * ProfitsController constructor.
     * @param $profitRepositories
     */
    public function __construct(ProfitsRepositories $profitRepositories)
    {
        $this->profitRepositories = $profitRepositories;
    }

    /**
     * 提交分润
     * @param Request $request
     * @param Accounts $accounts
     * @param ProfitLogs $profitLogs
     * @return array
     * @throws ParameterException
     * @route("api/v1/submit/profits","post")
     *
     */
    public function addProfit(Request $request,Accounts $accounts,ProfitLogs $profitLogs)
    {
        return $this->profitRepositories->addProfitsToParents($request,$accounts,$profitLogs,function($currentUser,$money){
           // 添加agent
            if(!empty($currentUser->uAgent)){
                throw new ParameterException(['msg' => '该用户已经购买过此产品']);
            }
            $agent_info = app()->Grades->init($money)->getLevels();
            $agents = [
                'ua_name' => $currentUser->u_nickname,
                'ua_level' => $agent_info['name'],
                'pay_money' => $money,
               // 'goods_money' => 3000 * $agent_info['pixel'] * 0.5 , // 测试用
                'goods_money' => 3000 * ($money/680) * 0.5 ,
            ];

            if(($agent_info['pixel']>=1)&&($agent_info['pixel']<10))
            {
                $currentUser->u_level = 1 ;
            }elseif ($agent_info['pixel'] >= 10){
                $currentUser->u_level = 2 ;
            }
            $currentUser->save();
            $user_agent = $currentUser->addAgents($agents);
            if(!$user_agent) {
                throw new ParameterException(['msg' => '购买失败']);
            }
        },function($currentUser,$money){
            // 添加divideLog
            $pay_money = $money ;
            $logs = $currentUser->addDivideLogs(compact('pay_money'));
            if(!$logs) {
                throw new ParameterException(['msg' => '购买失败']);
            }
        });
    }


    /**
     * @route("api/v1/profit","post")
     *
     */

    public function everyDaysToProfit()
    {
        return $this->profitRepositories->addProfitMq();
    }


}