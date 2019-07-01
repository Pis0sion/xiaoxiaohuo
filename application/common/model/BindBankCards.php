<?php


namespace app\common\model;


use think\Model;

/**
 * Class BindBankCards
 * @package app\common\model
 */
class BindBankCards extends Model
{
    protected $table = "axgy_user_bank_card";

    protected $createTime = "ubc_create_time";

    protected $updateTime = false ;

    protected $autoWriteTimestamp = "datetime" ;

    protected $pk = "ubc_id";

    /**
     * @param $value
     * @param $data
     * @return bool|string
     */
    public function getUbcNumAttr($value,$data)
    {
        if(!empty($value)){
            return substr($value,-4);
        }
    }

    /**
     * @param $cardNum
     * @param int $uid
     * @return mixed
     */
    public function isExist($cardNum, $uid = 0)
    {
        $where['uid'] = $uid;
        $where['ubc_num'] = $cardNum;
        return self::get(function($query)use($where){
            $query->where($where)->where('ubc_state','<>',9);
        });
    }

}