<?php


namespace app\common\model;


use app\lib\enum\Domain;
use PDOStatement;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Model;
use think\model\relation\HasMany;
use think\model\relation\HasOne;

/**
 * Class Users
 * @package app\common\model
 */
class Users extends Model
{

    protected $table = "axgy_user";

    protected $createTime = "u_create_time";

    protected $updateTime = false ;

    protected $autoWriteTimestamp = "datetime" ;


    /**
     * 用户会员等级
     * @return HasOne
     */
    public function uAgent()
    {
        return $this->hasOne(Agents::class,"uid","id");
    }

    /**
     * 添加代理商
     * @param $agents
     * @return false|Model
     */
    public function addAgents($agents)
    {
        return $this->uAgent()->save($agents);
    }

    /**
     * 用户账户
     * @return HasOne
     */
    public function uAccount()
    {
        return $this->hasOne(Accounts::class,"uid","id");
    }

    /**
     * 新增账户
     * @param array $account
     * @return false|Model
     */
    public function addAccount($account = [])
    {
        return $this->uAccount()->save($account);
    }

    /**
     * 用户实名信息
     * @return HasOne
     */
    public function certifications()
    {
        return $this->hasOne(Certifications::class,"uid","id");
    }

    /**
     * 添加用户实名信息
     * @param $certification
     * @return false|Model
     */
    public function addUsersToCertifications($certification = [])
    {
       return $this->certifications()->save($certification);
    }

    /**
     * 用户绑定的银行卡
     * @return HasMany
     */
    public function usersBankCards()
    {
        return $this->hasMany(BindBankCards::class,"uid","id")->where('ubc_state','<>',9);
    }

    /**
     * 用户默认的银行
     * @return array|PDOStatement|string|Model|null
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     */
    public function usersToDefaultBank()
    {
        return $this->usersBankCards()->where('ubc_is_default',1)->findOrEmpty();
    }

    /**
     * 添加用户的银行卡
     * @param $cards
     * @return false|Model
     */
    public function addBankCards($cards)
    {
        return $this->usersBankCards()->save($cards);
    }

    /**
     * 用户提现记录
     * @return HasMany
     */
    public function withdraws()
    {
        return $this->hasMany(Withdraws::class,"uid","id")->with("bank");
    }

    /**
     * 未审核提现订单
     * @return array|PDOStatement|string|Model|null
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     */
    public function isNotWithdraws()
    {
        return $this->withdraws()->where(['wo_state' => 0])->select();
    }

    /**
     * 提交提现表单
     * @param $records
     * @return false|Model
     */
    public function addWithdraws($records)
    {
        return $this->withdraws()->save($records);
    }

    /**
     * 添加分润日志
     * @return HasOne
     */
    public function divideLogs()
    {
        return $this->hasOne(DivideLogs::class,"uid","id");
    }

    /**
     * 添加日志
     * @param $logs
     * @return false|Model
     */
    public function addDivideLogs($logs)
    {
        return $this->divideLogs()->save($logs);
    }

    /**
     * @return HasMany
     */
    public function balanceLogs()
    {
        return $this->hasMany(UsersBalanceLogs::class,"uid","id");
    }

    /**
     * 积分记录
     * @return HasMany
     */
    public function integralLogs()
    {
        return $this->hasMany(UsersIntegrals::class,"uid","id");
    }

    /**
     * 推广奖励
     * @return HasMany
     */
    public function profitsLogs()
    {
        return $this->hasMany(ProfitLogs::class,"uid","id")->with('owner');
    }

    /**
     * 用户是否存在
     * @param $u_nickname
     * @return mixed
     */
    public static function userIsExists($u_nickname)
    {
        $where['u_nickname'] = $u_nickname;
        return self::getOrFail(function($query)use($where){
            $query->where($where)->whereIn('u_state','1,2')->order('u_create_time', 'desc');
        },"uAgent");
    }

    /**
     * 直推用户
     * @return HasMany
     */
    public function hasMembers()
    {
        return $this->hasMany(Users::class,"u_parent_uid","id");
    }

    /**
     * 父级用户
     * @return HasOne
     */
    public function hasParents()
    {
        return $this->hasOne(Users::class,"id","u_parent_uid");
    }

    /**
     * 该用户的订单
     * @return HasMany
     */
    public function hasIntegralOrders()
    {
        return $this->hasMany(OrdersByIntegral::class,"user_id",'id');
    }

    /**
     * 用户收货地址列表
     * @return HasMany
     */
    public function hasUsersConsigns()
    {
        return $this->hasMany(UserConsigns::class,"uid","id")
            ->field('uc_id,uc_consignee,uc_province_id,uc_city_id,uc_county_id,uc_province,uc_city,uc_county,uc_location,uc_phone,uc_is_default')
            ->where('uc_state','1');
    }

    /**
     * 添加用户收货地址
     * @param $consigns
     * @return false|Model
     */
    public function addUserConsigns($consigns)
    {
        return $this->hasUsersConsigns()->save($consigns);
    }

    /**
     * 获取用户的默认收货地址
     * @return array|PDOStatement|string|Model
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     */
    public function hasUsersDefaultConsigns()
    {
        return $this->hasUsersConsigns()
            ->where('uc_is_default','1')
            ->field('uc_id,uc_consignee,uc_province_id,uc_city_id,uc_county_id,uc_province,uc_city,uc_county,uc_location,uc_phone,uc_is_default')
            ->findOrEmpty();
    }

    /**
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getULevelAttr($value,$data)
    {
        $gradeText = [
            0 => '游客' ,
            1 => '供应商' ,
            2 => '总供应商' ,
        ];
        return $gradeText[$value];
    }

    /**
     * @param $value
     * @param $data
     * @return string
     */
    public function getUPhoneAttr($value,$data)
    {
        $phone = " ";
        if(!empty($value)){
            $phone = substr($value, 0, 3).'****'.substr($value, 7);
        }
        return $phone ;

    }

    /**
     * 获取当前注册几次
     * @return float|string
     */
    public function getClientIpCounts()
    {
        return $this->where(['u_create_ip'=>request()->ip()])->whereTime('u_create_time','today')->count();
    }

    /**
     * @param $value
     * @param $data
     * @return string
     */
    public function getUHeadPortraitAttr($value,$data)
    {
        $path = "";
        if(!empty($value))
        {
            $path = Domain::HEADURL.$value;
        }
        return $path ;
    }
}