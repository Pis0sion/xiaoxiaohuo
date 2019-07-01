<?php


namespace app\api\service\uploads;


class Images extends Uploads
{

    /**
     * @var
     * 文件类型
     */
    protected $mimeType = 'image';
    /**
     * @var
     * 文件大小
     */
    protected $fileSize = 1024 * 1024 * 2;
    /**
     * @var
     * 文件扩展
     */
    protected $fileExt = 'jpg,png,gif';

}