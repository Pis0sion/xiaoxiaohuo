<?php


namespace app\common\command;


use app\common\model\DivideLogs;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

class EveryDayToProfits extends Command
{

    protected function configure()
    {
        $this->setName('profits')->setDescription('Here is the every day profits ');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     */
    protected function execute(Input $input, Output $output)
    {
        $output->writeln("profits:开始执行");

        $users = $this->getAvalibleUsersToProfits();

        $stomp = new \Stomp('tcp://39.105.2.226:61613','pis0sion','zihuang2010=-0');

        $queue = "/queue/userProfits" ;

        $data = [];

        foreach ($users as $key=>$user){

            $data['uid'] = $user['uid'] ;

            $data['count'] = 200 - $user['count_days'] ;

            try {

                $stomp->begin("Transaction");

                $stomp->send($queue, json_encode($data), [
                    'persistent' => 'true',
                    'AMQ_SCHEDULED_DELAY' => 5 * 1000,
                ]);

                $stomp->commit("Transaction");

            }catch (\Throwable $e){

                $stomp->abort('Transaction');

                $this->LogError(json_encode($data));

            }

        }

        $output->writeln("分润开始执行");
    }

    /**
     * 获取有效的用户
     */
    public function getAvalibleUsersToProfits()
    {
        $allUsers = DivideLogs::where("is_open",1)
            ->where('count_days','>',0)
            ->field('uid,count_days')
            ->select();

        return $allUsers ;
    }

    /**
     * 记录失败的用户
     * @param $msg
     */
    public function LogError($msg)
    {
        Log::init([
            'type'  =>  'File',
            'path'  =>  app()->getRuntimePath(),
            'level' => ['error']
        ]);

        Log::record($msg,'error');
    }


}