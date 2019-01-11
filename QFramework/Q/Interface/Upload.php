<?php
/**
* 
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 上传对象
* @version v1.0
*/

interface Q_Interface_Upload
{
    /**
     * 获取文件路径信息
     *
     * @param array $file 文件信息
     * @param array $data
     * @return array ($path, $thumbPath);
     */
	public function save(array $file, array $data = array());
	public function rm($path);
}
