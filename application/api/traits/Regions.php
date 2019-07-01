<?php


namespace app\api\traits;


use app\lib\exception\ParameterException;

trait Regions
{

    public function obtainRegionsByCountyId($regions, $county_id)
    {
        $countyRes = $regions->isExist($county_id);
        if (!$countyRes) {
            throw new ParameterException(['msg' => "请正确选择开户行区、县"]);
        }

        $cityRes = $regions->isExist($countyRes->parentid);
        if (!$cityRes) {
            throw new ParameterException(['msg' => "请正确选择开户行城市"]);
        }

        $provinceRes = $regions->isExist($cityRes->parentid);
        if (!$provinceRes) {
            throw new ParameterException(['msg' => "请正确选择开户行省份"]);
        }

        return compact('countyRes', 'cityRes', 'provinceRes');
    }

    public function obtainUserInfos($users)
    {
        return new class($users)
        {
            public $id;
            public $u_phone;
            public $u_nickname;
            public $grade = "游客";
            public $u_parent_uid;
            public $u_parent_nickname = "无";
            public $u_head_portrait;
            public $goods_money = 0.00;
            public $end_goods_money = 0.00;
            public $ua_integral_value = 0.00;
            public $ua_available_value = 0.00;
            public $in_balance_coin = 0.00;
            protected $users;

            /**
             *  constructor.
             * @param $users
             */
            public function __construct($users)
            {
                $this->users = $users;
            }

            public function __invoke()
            {
                // TODO: Implement __invoke() method.
                $this->id = $this->users->id;
                $this->u_phone = $this->users->u_phone;

                $this->u_nickname = $this->users->u_nickname;
                $this->u_parent_uid = $this->users->u_parent_uid;
                $this->u_head_portrait = $this->users->u_head_portrait;
                if ($this->users->uAgent) {
                    $this->grade = $this->users->uAgent->ua_level;
                    $this->goods_money = $this->users->uAgent->goods_money;
                    $this->end_goods_money = $this->users->uAgent->end_goods_money;
                }
                if ($this->users->hasParents) {
                    $this->u_parent_nickname = $this->users->hasParents->u_nickname;
                }
                if ($this->users->uAccount) {
                    $this->ua_integral_value = $this->users->uAccount->ua_integral_value;
                    $this->ua_available_value = $this->users->uAccount->ua_available_value;
                    $this->in_balance_coin = $this->users->uAccount->in_balance_coin;
                }
                return $this;
            }
        };
    }

    public function addBindBankCards($bindBankCards,$request,$provinceRes,$cityRes,$countyRes,$bankInfo,$bankFlag,$cardType)
    {
        $bindBankCards->uid = app()->usersInfo->id;
        $bindBankCards->ubc_num = $request->cardNum;
        $bindBankCards->b_id = $request->bankId;
        $bindBankCards->ubc_name = $bankInfo->bank_name;
        $bindBankCards->ubc_bank_branch = $request->bankBranch;
        $bindBankCards->ubc_province_id = $provinceRes->id;
        $bindBankCards->ubc_city_id = $cityRes->id;
        $bindBankCards->ubc_county_id = $request->countyId;
        $bindBankCards->ubc_province = $provinceRes->areaname;
        $bindBankCards->ubc_city = $cityRes->areaname;
        $bindBankCards->ubc_county = $countyRes->areaname;
        $bindBankCards->ubc_flag = $bankFlag;
        $bindBankCards->ubc_card_type = $cardType;
        $bindBankCards->ubc_holder = $request->holder;
        $bindBankCards->ubc_state = 1;
        $bindBankCards->ubc_is_default = 0;
        $bindBankCards->ubc_create_ip = $request->ip();
        return $bindBankCards ;
    }

}