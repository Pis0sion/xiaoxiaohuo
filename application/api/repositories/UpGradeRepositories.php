<?php


namespace app\api\repositories;


use app\api\utils\Utils;

class UpGradeRepositories
{
    /**
     * @return array
     */
    public function upgradeVersion()
    {
		header("Content-type:text/json");
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        $post_appid = input("post.appid");
        $post_version = input("post.version");

        $appid = config("version.appid");
        $newversion = config("version.version");


        $rsp = [
            'status'  => 0 ,
            'note'    => '' ,
            'url'     => '' ,
        ];

        if (!empty($post_appid) && !empty($post_version)) {
            if ($post_appid === $appid) {
                if ($post_version !== $newversion) {
                    $rsp["status"] = 1;
                    $rsp["note"] = config("version.note");
					if(strpos($agent, 'android')){
						$rsp["url"] = config("version.android_url");
					}elseif(strpos($agent, 'iphone') || strpos($agent, 'ipad')){
						$rsp["url"] = config("version.ios_url");
					}
                }
            }
        }
//		$rsp = [
//			'status'  => 0 ,
//			'note'    => '' ,
//			'url'     => '' ,
//		];
        return json($rsp,200);
    }
}