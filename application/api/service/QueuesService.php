<?php


namespace app\api\service;

/**
 * Class QueuesService
 * @package app\api\service
 */
class QueuesService
{
    // 实例对象
    protected static $instance = [] ;
    // 连接资源
    protected $link ;
    // 队列名字
    protected $queue ;

    /**
     * QueuesService constructor.
     */
    protected function __construct()
    {
        $this->link = new \Stomp(config("queue.gateway"),config("queue.username"),config("queue.password"));
    }
    //  禁止clone
    protected function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return QueuesService|array
     */
    public static function getInstance()
    {
        if(!self::$instance instanceof self)
        {
            self::$instance = new self();
        }
        return self::$instance ;
    }

    /**
     * @return \Stomp
     */
    public function getLink(): \Stomp
    {
        return $this->link;
    }
    /**
     * @param mixed $queue
     */
    public function setQueue($queue): void
    {
        $this->queue = $queue;
    }

    /**
     * 推送消息
     * @param $data
     * @param array $options
     * @return mixed
     */
    public function push($data,$options = ['persistent'=> true])
    {
        return $this->link->send($this->queue,$data,$options);
    }
    /**
     * 订阅
     */
    private function subscribe()
    {
        $this->link->subscribe($this->queue);
    }

}