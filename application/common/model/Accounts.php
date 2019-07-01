<?php


namespace app\common\model;


use think\Model;

class Accounts extends Model
{
    protected $table = "axgy_user_account";

    protected $pk = "uid";

    protected $createTime = "ua_create_time";

    protected $updateTime = "ua_update_time" ;

    protected $autoWriteTimestamp = "datetime" ;

    /**
     * @param $uid
     * @return Accounts|array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function IsUserAccount($uid){
        $isAccount =  self::where('uid',$uid)->find();
        if(!$isAccount){
            $userAccountData  = [
                'uid'=>$uid
            ];
            $isAccount = self::create($userAccountData);
        }
        return $isAccount ;
    }
}