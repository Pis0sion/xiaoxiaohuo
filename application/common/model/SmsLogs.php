<?php


namespace app\common\model;


use app\lib\exception\ParameterException;
use think\Model;

class SmsLogs extends Model
{
    protected $table = "axgy_sms_log";

    protected $pk = "sl_id";

    protected $createTime = "sl_create_time";

    protected $updateTime = "sl_update_time";

    protected $autoWriteTimestamp = "datetime" ;
    /**
     * 存在是否发送的验证码记录
     * @param $mobile
     * @param $scene
     * @return mixed
     */
    public function isExist($mobile,$scene)
    {
        $where['sl_phone'] = $mobile;
        $where['sl_scene'] = $scene;
        return self::get(function($query)use($where){
            $query->where($where)->whereIn('sl_state','0,1')->order('sl_id', 'desc');
        });
    }

    /**
     * 验证验证码有效
     * @param $phone
     * @param $code
     * @param $scene
     * @return bool
     * @throws ParameterException
     */
    public function isValidCode($phone, $code, $scene)
    {
        //验证码是否存在
        $isExist = $this->isExist($phone, $scene);
        if (!$isExist) {
            throw new ParameterException(['msg' => "请输入正确手机验证码！"]);
        }
        $isExist->setInc('sl_time', 1);
        if ($isExist->sl_time >= 5) {
            throw new ParameterException(['msg' => "您已请求5次了，请稍后重试！"]);
        }
        //是否在有效期内
        $sendTime = $isExist->sl_create_time;
        $intervalTime = time() - strtotime($sendTime);
        $availableTime = 30 * 60;//30分钟有效期
        if ($intervalTime > $availableTime) {
            throw new ParameterException(['msg' => "验证码已过期，请再次获取！"]);
        }
        //是否正确
        $params = json_decode($isExist->sl_request, true);
        if ($params == '' || !array_key_exists('code', $params)) {
            throw new ParameterException(['msg' => "请先获取手机验证码！"]);
        }
        if ($code != $params['code']) {
            throw new ParameterException(['msg' => "请输入有效的手机验证码！"]);
        }
        return true ;
    }

    /**
     * 更新状态
     * @param $phone
     * @param $scene
     */
    public function setUsedState($phone, $scene)
    {
        $isExist = $this->isExist($phone, $scene);
        $isExist->sl_state = 3 ;
        $isExist->save();
    }

    /**
     * 设置过期
     * @param $id
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setExpireState($id)
    {
        self::where('sl_id', $id)->update(['sl_state' => 4]);
    }
}