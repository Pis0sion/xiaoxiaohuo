<?php


namespace app\api\repositories;


use app\api\utils\Utils;
use app\common\validate\WithdrawValidate;
use app\lib\exception\ParameterException;
use think\Db;

/**
 * Class AccountsRepositories
 * @package app\api\repositories
 */
class AccountsRepositories
{
    /**
     * 提现
     * @param $request
     * @param $bindBankCards
     * @param \Closure $verifyMoney
     * @return array
     * @throws ParameterException
     */
    public function addWithdraw($request,$bindBankCards,\Closure $verifyMoney)
    {
        // 验证
        (new WithdrawValidate())->goCheck();

        $bind_bank = $bindBankCards::get($request->bc_id);
        // 查询卡的状态
        if(!$bind_bank || ($bind_bank->ubc_state != 1) ) {
            throw new ParameterException(['msg' => "银行卡信息不存在"]);
        }
        // 查询用户账户状态
        if(!app()->usersInfo->uAccount && (app()->usersInfo->uAccount->ua_state != 1) && (app()->usersInfo->uAccount->is_withdraw != 1) ) {
            throw new ParameterException(['msg' => "用户账户异常"]);
        }

        // 查询未审核的提现订单

        $isNotWithdraw = app()->usersInfo->isNotWithdraws();

        if(!$isNotWithdraw->isEmpty()){
            foreach ($isNotWithdraw as $item){
                $notAllow = date('Y-m-d',strtotime($item->wo_create_time)) ;
                if($notAllow == "2019-03-05" || $notAllow == "2019-03-07") {
                    continue ;
                }
                throw new ParameterException(['msg' => "存在未处理的提现,不能再次提现！"]);
            }
        }

        // 验证金额是否合法
        $verifyMoney($request->money);

        Db::startTrans();

        try{
            $available_value = app()->usersInfo->uAccount()->lock(true)->value("ua_available_value");
            if ($request->money > $available_value) {
                throw new ParameterException(['msg' => "提现金额不大于账户余额!"]);
            }
            $withdraws = [
                'bc_id' =>  $request->bc_id ,
                'wo_money' => $request->money ,
                'desc' => '扣除：0 ，到账：'.$request->money,
                'wo_money_before' => $available_value ,
                'wo_money_after' => bcsub($available_value,$request->money,2) ,
                'wo_create_ip' =>  $request->ip() ,
                'wo_mold' =>  "user" ,
            ];
            // 添加提现的记录
            if(app()->usersInfo->addWithdraws($withdraws)){
                app()->usersInfo->uAccount->ua_available_value = bcsub($available_value,$request->money,2) ;
                app()->usersInfo->uAccount->save();
                Db::commit();
                return Utils::renderJson("操作成功，等待审核");
            }
            throw new ParameterException();
        }catch (\Throwable $e){
            Db::rollback();
            throw new ParameterException(['msg' => '操作失败，请稍后再试']);
        }
    }
}