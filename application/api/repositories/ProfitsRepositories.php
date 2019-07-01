<?php


namespace app\api\repositories;


use app\api\utils\Utils;
use app\common\model\Users;
use app\lib\exception\ParameterException;
use think\Db;

/**
 * Class ProfitsRepositories
 * @package app\api\repositories
 */
class ProfitsRepositories
{

    public function addProfitMq()
    {

        $uid = request()->param("uid");
        // 查询该用户是否正常
        $user = Users::findOrEmpty($uid);

        if ($user->isEmpty()) {
            throw new ParameterException(['msg' => '用户不存在或者状态异常']);
        }

        if (empty($user->divideLogs)) {
            throw new ParameterException(['msg' => '用户数据异常']);
        }

        if (empty($user->uAgent)) {
            throw new ParameterException(['msg' => '用户数据异常']);
        }

        if ($user->divideLogs->is_open != 0) {
            throw new ParameterException(['msg' => '请不要重复提交']);
        }


        $stomp = app()->Utils->activeMq();

        $stomp->setQueue("/queue/userProfits");

        try {

            $stomp->getLink()->begin("Transaction");
            /**
             * 更新分红记录
             */
            $user->divideLogs->is_open = 1;
            $user->divideLogs->open_time = date("Y-m-d H:i:s", time());
            $user->divideLogs->save();
            $user->uAgent->is_open = 1 ;
            $user->uAgent->save();

            $count = 200 - $user->divideLogs->count_days;
            /**
             * 添加到队列中
             */
            $data = [
                'uid' => $uid,
                'count' => $count,
            ];

            $stomp->push(json_encode($data), [
                'persistent'=> true,
                'AMQ_SCHEDULED_DELAY' => 5 * 1000,
            ]);

            $stomp->getLink()->commit("Transaction");

            return Utils::renderJson("分润开始执行");
        } catch (\Throwable $e) {

            $stomp->getLink()->abort('Transaction');

            return Utils::renderJson("分润执行失败");
        }
    }

    /**
     * 上级分润
     * @param $request
     * @param $accounts
     * @param $profitLogs
     * @param \Closure $addAgents
     * @param \Closure $divideLog
     * @return array
     * @throws ParameterException
     */
    public function addProfitsToParents($request, $accounts, $profitLogs, \Closure $addAgents, \Closure $divideLog)
    {

        $uid = $request->uid;
        $money = $request->money;

        if (!($money % 680 == 0)) {
            throw new ParameterException(['msg' => '金额不符合规定']);
        }

        $parent = 0;
        $indirect = 0;
        // 返回当前对象
        $currentUser = $this->getAvalibaleToUser($uid);

        if ($currentUser->isEmpty()) {
            throw new ParameterException(['msg' => '用户不存在']);
        }
        // 首先查询用户升级是否已经分润
        $isExist = $profitLogs->get(function ($query) use ($uid) {
            $query->where('pl_buyer_id', $uid);
        });
        if ($isExist) {
            throw new ParameterException(['msg' => '请不要重复提交该用户分润']);
        }
        //  添加代理商等级
        $addAgents($currentUser, $money);
        //  添加分红日志
        $divideLog($currentUser, $money);
        //  TODO;  一次性添加积分



        if ($currentUser->u_parent_uid == 0) {
            return Utils::renderJson("提交成功");
        }
        //  获取直推和间推用户
        if (!empty($currentUser) && ($currentUser->u_parent_uid != 0)) {
            $parentUser = $this->getAvalibaleToUser($currentUser->u_parent_uid);

            if (!($parentUser->isEmpty())) {
                $parent = $parentUser->id;
                ($parentUser->u_parent_uid != 0)
                && !($this->getAvalibaleToUser($parentUser->u_parent_uid))->isEmpty()
                && ($indirect = $this->getAvalibaleToUser($parentUser->u_parent_uid)->id);
            }
        }
        // 获取分润
        $profits = app()->Profits->init($money, $parent, $indirect)->handler();

        $insert = [];
        // 有分润
        foreach ($profits as $key => $profit) {
            $data = [
                'uid' => $profit['uid'],
                'pl_pay_money' => $money,
                'pl_remark' => $currentUser->u_nickname . ' 缴费 ' . $money ,
                'pl_money' => $profit['profit'],
                'pl_create_ip' => request()->ip(),
                'pl_buyer_id' => $currentUser->id,
                'pl_rate' => $profit['rate'],
                'earnings_type' => $profit['type'],
            ];
            $insert[] = $data;
        }
        $userProfit = array_column($profits, 'profit', 'uid');

        Db::startTrans();
        try {
            if ($parent != 0)
                $this->addProfitsTousers($parent, $userProfit, $accounts);
            if ($indirect != 0)
                $this->addProfitsTousers($indirect, $userProfit, $accounts);
            $profitLogs->saveAll($insert);
            Db::commit();
            return Utils::renderJson("提交成功");
        } catch (\Throwable $e) {
            Db::rollback();
            throw new ParameterException(['msg' => '提交失败']);
        }

    }

    /**
     * @param $uid
     * @param $userProfit
     * @param $accounts
     */
    private function addProfitsTousers($uid, $userProfit, $accounts)
    {
        $accounts->IsUserAccount($uid);
        $user_account = $accounts->where('uid', $uid)->lock(true)->find();
        $user_account->in_balance_coin = bcadd($user_account->in_balance_coin, $userProfit[$uid]);
        $user_account->ua_available_value = bcadd($user_account->ua_available_value, $userProfit[$uid]);
        $user_account->ua_integral_value = bcadd($user_account->ua_available_value, $userProfit[$uid]);
        $user_account->save();
    }

    /**
     * 获取有效的用户
     * @param $uid
     * @return mixed
     */
    private function getAvalibaleToUser($uid)
    {
        return $this->isUsersExist($uid, function ($uid) {
            return Users::where(['id' => $uid])->whereIn('u_state', '1,2')->findOrEmpty();
        });
    }

    /**
     * 判断是否存在用户
     * @param $uid
     * @param \Closure $isExist
     * @return mixed
     */
    private function isUsersExist($uid, \Closure $isExist)
    {
        return $isExist($uid);
    }


}