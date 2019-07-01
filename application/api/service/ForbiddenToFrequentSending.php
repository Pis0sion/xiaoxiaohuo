<?php


namespace app\api\service;


use app\api\utils\Utils;
use app\common\model\SmsLogs;
use app\lib\exception\ParameterException;
use Pikirasa\RSA;

/**
 * Class ForbiddenToFrequentSending
 * @package app\api\service
 */
class ForbiddenToFrequentSending
{
    /**
     * 手机号
     * @var
     */
    protected $mobile ;
    /**
     * 场景
     * @var
     */
    protected $scene ;
    /**
     * 客户端
     * @var
     */
    protected $client ;
    /**
     * 日志实例
     * @var SmsLogs
     */
    protected $smsLogs ;
    /**
     * 队列
     * @var
     */
    protected $queue ;

    /**
     * ForbiddenToFrequentSending constructor.
     * @param $smsLogs
     */
    public function __construct(SmsLogs $smsLogs)
    {
        $this->smsLogs = $smsLogs;
    }

    /**
     * 初始化
     * @param $mobile
     * @param $scene
     * @param $client
     * @return $this
     */
    public function init($mobile,$scene,$client)
    {
        $this->mobile = $mobile ;
        $this->scene = $scene ;
        $this->client = $client ;
        $this->queue = "/queue/sendSms";
        return $this ;
    }
    /**
     * 加密
     * @param $mobile
     * @param $code
     * @return false|string
     */
    protected function encrypt($mobile,$code)
    {
        $rsa = new RSA(config("encrypt.publicKey"),config("encrypt.privateKey"));
        $data = json_encode(compact('mobile','code'));
        $encrypted = base64_encode($rsa->encrypt($data));
        return json_encode(compact('encrypted'));
    }

    /**
     * 发送验证码
     * @param $desc
     * @return array
     * @throws ParameterException
     * @throws \Throwable
     */
    public function push($desc)
    {
        try {
            $sms = $this->isPush($desc);
            $code = rand(1000, 9999);
            $data = $this->encrypt($sms->sl_phone, $code);
            $stomp = app()->Utils->activeMq();
            $stomp->setQueue($this->queue);
            if ($stomp->push($data)) {
                $sms->sl_request = json_encode(compact('code'));
                $sms->save();
                return Utils::renderJson("发送成功");
            }
        }catch (\Throwable $e){
            $msg = "发送失败";
            if($e instanceof ParameterException) {
                $msg = $e->msg ;
            }
            throw new ParameterException(['msg' => $msg]);
        }
    }

    /**
     * 判断是否可以重新发送验证码
     * @param $desc
     * @return SmsLogs
     * @throws \Throwable
     */
    protected function isPush($desc)
    {
        try {
            if($slRes = $this->getLastRecords())
            {
                $this->verifyMobile($slRes) ;
            }
            //写入发送记录
            $data = [
                'sl_phone' => $this->mobile,
                'sl_scene' => $this->scene,
                'sl_desc' => $desc,
                'sl_client' => $this->client,
                'sl_create_ip' => request()->ip(),
            ];

            return SmsLogs::create($data);

        } catch (\Throwable $e) {
            throw $e ;
        }
    }

    /**
     * @return mixed
     */
    protected function getLastRecords()
    {
        return $this->smsLogs->isExist($this->mobile,$this->scene);
    }

    /**
     * 验证手机验证码是否合法
     * @param $slRes
     * @throws ParameterException
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    protected function verifyMobile($slRes)
    {
        $interval = time() - strtotime($slRes->sl_create_time);
            //判断是否频繁请求
        if ($interval < 120) {
            throw new ParameterException([
                'msg' => '请勿短时间内频繁请求短信'
            ]);
        }
            //判断是否在有效期内
        if ($interval < 600 && $slRes->sl_state == 1) {
            throw new ParameterException([
                'msg' => '验证码有效期为5分钟,勿频繁请求'
            ]);
        }
        $this->smsLogs->setExpireState($slRes->sl_id);
    }



}