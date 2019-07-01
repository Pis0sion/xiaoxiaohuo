<?php


namespace app\api\service\uploads;


use app\lib\exception\ParameterException;

/**
 * Class Uploads
 * @package app\api\service\uploads
 */
abstract class Uploads
{
    /**
     * @var
     * 文件类型
     */
    protected $mimeType ;
    /**
     * @var
     * 文件大小
     */
    protected $fileSize ;
    /**
     * @var
     * 文件扩展
     */
    protected $fileExt ;
    /**
     * @var
     * 上传文件
     */
    protected $fileStorage ;

    /**
     * @param $request
     * @return $this
     */
    public function init($request)
    {
        $this->fileStorage = $request->file($this->mimeType);
        return $this ;
    }

    /**
     * @param $savePath
     * @return mixed
     * @throws ParameterException
     */
    public function upload($savePath)
    {
        /**
         * 断言
         */
        $asset = [
            'size' => $this->fileSize,
            'ext' => $this->fileExt,
        ];
        /**
         * 接收数据
         */
        try{
            $info = $this->fileStorage->validate($asset)->move($savePath);

            if($info) {
                return $info->getSaveName();
            }else{
                throw new ParameterException(['msg' => $this->fileStorage->getError()]);
            }
        }catch (\Throwable $e){
            if($e instanceof ParameterException){
                throw new ParameterException(['msg' => $e->msg]);
            }
            throw new ParameterException(['msg' => "上传失败"]);
        }

    }
}