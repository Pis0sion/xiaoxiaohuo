<?php


namespace app\api\repositories;


use app\api\traits\Regions;
use app\api\utils\Utils;
use app\common\model\{Banks,BindBankCards,Users};
use app\common\validate\{BindBankCardValidate, CertificationValidate, ConsignsValidate, NickNameValidate};
use app\lib\exception\ParameterException;
use think\exception\DbException;

/**
 * Class UsersRepositories
 * @package app\api\repositories
 */
class UsersRepositories
{
    use Regions ;

    /**
     * 获取用户信息
     * @return array
     */
    public function getUsers()
    {
        // 获取用户信息
        $users = ($this->obtainUserInfos(app()->usersInfo))();
        return Utils::renderJson($users);
    }

    /**
     * 修改头像
     * @param $request
     * @param \Closure $closure
     * @return array
     * @throws ParameterException
     */
    public function receiveFiles($request,\Closure $closure)
    {
        $origin = $origin = "./uploads/".app()->usersInfo->u_head_portrait ;
        app()->usersInfo->u_head_portrait = app()->Images->init($request)->upload("./uploads") ;
        if(app()->usersInfo->save()){
            $closure($origin);
            return Utils::renderJson("修改成功");
        }
        throw new ParameterException(['msg' => '修改失败']);
    }

    /**
     * 修改昵称
     * @param $request
     * @return array
     * @throws ParameterException
     */
    public function alterUsersNickName($request)
    {
        (new NickNameValidate())->goCheck();
        app()->usersInfo->u_nickname = $request->nickname ;
        try{
            $result = app()->usersInfo->save();
            if($result) {
                return Utils::renderJson('修改成功');
            }
            throw new ParameterException();
        }catch (\Throwable $e){
            throw new ParameterException(['msg' => '修改失败']);
        }

    }

    /**
     * 查询用户实名认证的信息
     * @return array
     */
    public function getCertifications()
    {
        $users = $this->getUserToCertifications(app()->usersInfo->certifications);
        return Utils::renderJson($users);
    }

    /**
     * 获取认证信息
     * @param $certifications
     * @return
     */
    private function getUserToCertifications($certifications)
    {
        return (new class($certifications){
            public $status = -1 ;
            public $message = "无实名认证信息" ;

            /**
             *  constructor.
             * @param $certifications
             */
            public function __construct($certifications)
            {
                if(!empty($certifications)){
                    $this->status = 1 ;
                    $this->message = $certifications ;
                }
            }
        });
    }

    /**
     * 提交实名认证
     * @param $request
     * @param $smsLogs
     * @param \Closure $closure
     * @return array
     * @throws ParameterException
     */
    public function certification($request,$smsLogs,\Closure $closure)
    {
        (new CertificationValidate())->goCheck();
        // 验证手机验证码
        $smsLogs->isValidCode($request->mobile,$request->smsCode,"certification") ;
        // 验证是否重复提交或者已认证
        if(app()->usersInfo->certifications) {
            throw new ParameterException(['msg' => '请勿重复提交']);
        }
        $certification = [
            'ucer_real_name' => $request->realname ,
            'ucer_ID_num' => $request->idcard ,
            'ucer_create_ip' => $request->ip() ,
        ];
        if(app()->usersInfo->addUsersToCertifications($certification)) {
            $closure($smsLogs);
            return Utils::renderJson("提交成功");
        }
        throw new ParameterException(['msg' => '提交失败']);
    }

    /**
     * 绑定银行卡的信息
     * @return array
     */
    public function bankLists()
    {
        $banks = app()->usersInfo->usersBankCards()->field("ubc_id,ubc_name,ubc_num,ubc_is_default,ubc_state")->select();
        return Utils::renderJson(compact('banks'));
    }

    /**
     * 解绑银行卡信息
     * @param $bindBankCards
     * @param \Closure $strategy
     * @return array
     * @throws ParameterException
     */
    public function delBanks($bindBankCards,\Closure  $strategy)
    {
        if($bindBankCards->isEmpty() || ($bindBankCards->ubc_state != 1)) {
            throw new ParameterException(['msg' => '银行卡不存在或状态不正常']);
        }
        $strategy($bindBankCards);
        $bindBankCards->ubc_state = 9 ;
        $bindBankCards->ubc_is_default = 0 ;
        if($bindBankCards->save()){
            return Utils::renderJson("操作成功");
        }
        throw new ParameterException(['msg' => '操作失败']);
    }

    /**
     * 设置默认的银行卡
     * @param $bindBankCards
     * @param \Closure $strategy
     * @return array
     * @throws ParameterException
     */
    public function setBanksToDefault($bindBankCards,\Closure $strategy)
    {
        if($bindBankCards->isEmpty() || ($bindBankCards->ubc_state != 1) || ($bindBankCards->ubc_is_default == 1)) {
            throw new ParameterException(['msg' => '银行卡不存在或状态不正常']);
        }
        $strategy($bindBankCards);
        // 判断是否存在默认银行卡
        $default = app()->usersInfo->usersToDefaultBank();
        if($default){
            $default->ubc_is_default = 0 ;
            $default->save();
        }
        $bindBankCards->ubc_is_default = 1 ;
        if($bindBankCards->save()){
            return Utils::renderJson("操作成功");
        }
        throw new ParameterException(['msg' => '操作失败']);
    }

    /**
     * 绑定银行卡
     * @param $request
     * @param $bindBankCards
     * @param $smsLogs
     * @param $regions
     * @param $banks
     * @param \Closure $verify
     * @return array
     * @throws ParameterException
     */
    public function bindBankCard($request, $bindBankCards,$smsLogs,$regions,$banks,\Closure $verify)
    {
        // 验证
        (new BindBankCardValidate())->goCheck();
        // 验证是否绑定该银行卡
        if ($bindBankCards->isExist($request->cardNum, app()->usersInfo->id)) {
            throw new ParameterException(['msg' => "请不要重复添加银行卡"]);
        }
        $bankInfo = $banks::get(function ($query) use ($request) {
            $query->where(['b_id' => $request->bankId, 'b_state' => 1]);
        });
        if (!$bankInfo) {
            throw new ParameterException(['msg' => "请正确选择开户行！"]);
        }
        // 验证卡号是否正常
        $user_banks = $verify($request->cardNum);
        if (!$user_banks->validated) {
            throw new ParameterException(['msg' => "请输入有效的银行卡号"]);
        }
        $cardType = $user_banks->cardType;  //卡类型：DC|储蓄卡，CC|信用卡，PC|预付费卡，SCC|准贷记卡
        $bankFlag = $user_banks->bank;  //银行标识码
        // 验证验证码
        $smsLogs->isValidCode($request->mobile, $request->smsCode, "bindBankCard");

        extract($this->obtainRegionsByCountyId($regions,$request->countyId));

        $bindBankCards = $this->addBindBankCards($bindBankCards,$request,$provinceRes,$cityRes,$countyRes,$bankInfo,$bankFlag,$cardType);

        try {
            if ($bindBankCards->save()) {
                $smsLogs->setUsedState($request->mobile, "bindBankCard");
                return Utils::renderJson("绑定成功");
            }
            throw new ParameterException();
        } catch (\Throwable $e) {
            throw new ParameterException(['msg' => '绑定失败']);
        }
    }

    /**
     * @param $request
     * @return array
     * @throws DbException
     * @throws ParameterException
     */
    public function getUsesToDirect($request)
    {
        $ids = app()->usersInfo->hasMembers->column("id");
        $jiantui_count = 0 ;
        $zhitui_count = count($ids);
        if($zhitui_count != 0) {

            $jiantui_count = Users::field("id,u_level,u_nickname,u_head_portrait,u_phone,u_create_time")
                ->whereIn('u_parent_uid',implode(",",$ids))
                ->count();
        }
        $data = [];
        $type = $request->param("type",'zhitui');
        if($type == "zhitui"){

            $users = app()->usersInfo->hasMembers()->field("id,u_level,u_nickname,u_head_portrait,u_phone,u_create_time")->paginate(10);
            if($users->getCurrentPage() == 1) {
                $data['total'] = $users->total();
                $data['total_page'] = ceil($data['total'] / 10);
            }
            $data['users'] = $users->getCollection() ;
        }elseif($type == "jiantui"){
            if($zhitui_count != 0) {
                $users = Users::field("id,u_level,u_nickname,u_head_portrait,u_phone,u_create_time")
                    ->whereIn('u_parent_uid',implode(",",$ids))
                    ->paginate(10);
                if($users->getCurrentPage() == 1) {
                    $data['total'] = $users->total(); ;
                    $data['total_page'] = ceil($data['total'] / 10);
                }
                $data['users'] = $users->getCollection() ;
            }else{
                $data['total'] = 0 ;
                $data['total_page'] = 0 ;
                $data['users'] = [] ;
            }
        }else{
            throw new ParameterException(['msg' => '参数不合法']);
        }
        return Utils::renderJson(compact('zhitui_count','jiantui_count','data')) ;

    }

    /**
     * 获取默认银行卡
     * @return array
     */
    public function getUsersToDefaultBanks($request)
    {
        $bc_id= $request->param("bc_id","");
        if(!empty($bc_id)) {
            $bank = BindBankCards::get($bc_id);
            return Utils::renderJson(compact('bank'));
        }
        $bank =app()->usersInfo->usersToDefaultBank();
        return Utils::renderJson(compact('bank'));
    }

    /**
     * @param $request
     * @return array
     * @throws ParameterException
     */
    public function getUserProfitLog($request)
    {
        $time = $request->param("year_month"," ") ;
        if($time == " ") {
            throw new ParameterException(['msg' => '参数不对']);
        }
        $totalProfit = app()->usersInfo->balanceLogs()->sum('number');
        $start_time = $time."-01" ;
        $end_time = $time."-31";
        $details = app()->usersInfo->balanceLogs()->whereBetweenTime('createtime',$start_time,$end_time)->order("createtime desc")->select();
        $data['totalProfit'] = $totalProfit ;
        $data['details'] = $details;
        return Utils::renderJson($data);

    }

    /**
     * 积分列表
     * @return array
     */
    public function getUseruaIntegrals()
    {
        $totalProfit = app()->usersInfo->integralLogs()->sum('number');
        $details = app()->usersInfo->integralLogs()->field("desc,createtime,number")->order("createtime desc")->paginate(10);
        $data = Utils::render()->call($details);
        $data['total_score'] = $totalProfit ;
        return Utils::renderJson($data);
    }

    /**
     * 推荐奖励
     * @return array
     */
    public function getTeamProfits()
    {
        $totalProfit = app()->usersInfo->profitsLogs()->sum('pl_money');
        $details = app()->usersInfo->profitsLogs()->field("pl_remark,earnings_type,pl_create_time,pl_money,pl_buyer_id")->order("pl_create_time desc")->paginate(10);
        $data = Utils::render()->call($details);
        $data['total_profit'] = $totalProfit ;
        return Utils::renderJson($data);
    }

    /**
     * 提现列表
     * @return array
     */
    public function withdrawList()
    {
        $totalProfit = app()->usersInfo->withdraws()->sum('wo_money');

        $details = app()->usersInfo->withdraws()
            ->order('wo_create_time desc')->select();

        $data = [];

        foreach ($details as $detail){

            $notAllow = date('Y-m-d',strtotime($detail->wo_create_time)) ;
            if($notAllow == "2019-03-05" || $notAllow == "2019-03-07")
            {
                if ($detail->wo_state == 0){
                    continue ;
                }
            }
            $data[] = $detail ;
        }

        $data['details'] = $data;
        $data['total_profit'] = $totalProfit ;

        return Utils::renderJson($data);
    }

    /**
     * 获取银行卡列表
     * @return array
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bankList()
    {
        $lists = Banks::where('b_state','1')->select();
        return Utils::renderJson($lists);
    }

    /**
     * 获取地址列表
     * @return array
     */
    public function getAllConsigns()
    {
        $list = app()->usersInfo->hasUsersConsigns ;
        return Utils::renderJson(compact('list'));
    }

    /**
     * 获取用户的默认收获地址
     * @param $request
     * @return array
     */
    public function getDefaultConsigns($request)
    {
        $uc_id= $request->param("uc_id","");
        if(!empty($uc_id) && ($uc_id != 0)) {
            $result = app()->usersInfo->hasUsersConsigns()->where('uc_id',$uc_id)->findOrEmpty();
        }else{
            $result = app()->usersInfo->hasUsersDefaultConsigns();
        }
        return Utils::renderJson($result);
    }

    /**
     * 添加地址
     * @param $request
     * @param $regions
     * @return array
     * @throws ParameterException
     */
    public function addUsersConsigns($request,$regions)
    {
        (new ConsignsValidate())->goCheck();

        extract($this->obtainRegionsByCountyId($regions,$request->uc_county));

        $userConsign = array(
            'uc_consignee'  => $request->uc_consignee ,
            'uc_province_id'  => $provinceRes->id ,
            'uc_city_id'  => $cityRes->id ,
            'uc_county_id'  => $request->uc_county ,
            'uc_province'  => $provinceRes->areaname ,
            'uc_city'  => $cityRes->areaname ,
            'uc_county'  => $countyRes->areaname ,
            'uc_location'  => $request->uc_location ,
            'uc_phone'  => $request->uc_phone ,
            'uc_state'  => 1 ,
            'uc_create_ip'  => $request->ip() ,
            'uc_mold'  => 'user' ,
        );

        $result = app()->usersInfo->addUserConsigns($userConsign);
        if($result) {
            return Utils::renderJson("添加成功");
        }
        throw new ParameterException(['msg' => '添加地址失败']);
    }

    /**
     * 设置默认地址
     * @param $consigns
     * @param \Closure $strategy
     * @return array
     * @throws ParameterException
     */
    public function setConsignsToDefault($consigns,\Closure $strategy)
    {
        if($consigns->isEmpty() || ($consigns->uc_state != 1) || ($consigns->uc_is_default == 1)) {
            throw new ParameterException(['msg' => '默认地址不存在或状态不正常']);
        }
        $strategy($consigns);
        // 判断是否存在默认地址
        $default = app()->usersInfo->hasUsersDefaultConsigns();
        if($default){
            $default->uc_is_default = 0 ;
            $default->save();
        }
        $consigns->uc_is_default = 1 ;
        if($consigns->save()){
            return Utils::renderJson("操作成功");
        }
        throw new ParameterException(['msg' => '操作失败']);
    }

    /**
     * 编辑收货地址
     * @param $request
     * @param $consigns
     * @param \Closure $strategy
     * @return array
     * @throws ParameterException
     */
    public function editConsignsToDefault($request,$consigns,\Closure $strategy)
    {
        (new ConsignsValidate())->goCheck();
        if($consigns->isEmpty() || ($consigns->uc_state != 1)) {
            throw new ParameterException(['msg' => '默认地址不存在或状态不正常']);
        }
        $strategy($consigns);

        ($request->uc_consignee != $consigns->uc_consignee) && ($consigns->uc_consignee = $request->uc_consignee) ;
        ($request->uc_phone != $consigns->uc_phone) && ($consigns->uc_phone = $request->uc_phone) ;
        ($request->uc_location != $consigns->uc_location) && ($consigns->uc_location = $request->uc_location) ;
        if($request->uc_county != $consigns->uc_county_id) {
            extract($this->obtainRegionsByCountyId(model("Regions"),$request->uc_county));
            $consigns->uc_province_id = $provinceRes->id ;
            $consigns->uc_city_id = $cityRes->id ;
            $consigns->uc_county_id = $request->uc_county ;
            $consigns->uc_province = $provinceRes->areaname ;
            $consigns->uc_city = $cityRes->areaname ;
            $consigns->uc_county = $countyRes->areaname ;
        }

        if($consigns->save()){
            return Utils::renderJson("修改成功");
        }

        throw new ParameterException(['msg' => '修改失败']);

    }

    /**
     * 删除地址
     * @param $consigns
     * @param \Closure $strategy
     * @return array
     * @throws ParameterException
     */
    public function delUserConsigns($consigns,\Closure $strategy)
    {
        if($consigns->isEmpty() || ($consigns->uc_state != 1)) {
            throw new ParameterException(['msg' => '收货地址不存在或状态不正常']);
        }
        $strategy($consigns);
        $consigns->uc_state = 9 ;
        $consigns->uc_is_default = 0 ;
        if($consigns->save()){
            return Utils::renderJson("删除成功");
        }
        throw new ParameterException(['msg' => '删除失败']);
    }


}