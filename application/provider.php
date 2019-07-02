<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用容器绑定定义
return [
    "Utils"   =>   \app\api\utils\Utils::class ,

    "ForbiddenToFrequent"   =>   \app\api\service\ForbiddenToFrequentSending::class ,

    "Images"   =>   \app\api\service\uploads\Images::class ,

    "Profits"  =>  \app\api\service\ProfitsToParents::class ,

    "Grades"   => \app\api\service\AgentsLevelService::class ,

    "Mode"  =>  \app\api\service\ModeOfPayments::class ,
];
