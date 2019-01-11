<?php

class Admin_Page_Upload extends Admin_Page_Abstract
{
    public function doUpload(Q_Request $input, Q_Response $output)
    {
        $output->vali = Q_Validate::instance()->setRules($this->_getRules($input, $output))->setParams(['e' => 'exec']);
        $output->clearLayout()->setTemplate();
    }

    public function validate(Q_Request $input, Q_Response $output)
    {
        return true;
    }

    protected static function _getRules(Q_Request $input, Q_Response $output)
    {

        $_rules = [
            'file' => [
                'name'     => '上传图片',
                'required' => ['message' => '图片不能为空'],
            ],
        ];
        return $_rules;
    }

    public function doUploadImage(Q_Request $input, Q_Response $output)
    {
        $fileInfo = $input->files('file');

        //大小 0不限止
        $maxsize = 0;

        if ($fileInfo['error'] > 0) {
            switch ($fileInfo['error']) {
                case 1:
                    $error = "上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值";
                    break;
                case 2:
                    $error = "上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值";
                    break;
                case 3:
                    $error = "文件只有部分被上传。";
                    break;
                case 4:
                    $error = "没有文件被上传。";
                    break;
                case 6:
                    $error = "找不到临时文件夹";
                    break;
                case 7:
                    $error = "文件写入失败";
                    break;
                default:
                    $error = "未知错误，请稍后再试...";
            }
        }

        //定义允许类型
        $typearr = array("image/jpeg", "image/png", "image/gif");
        //判断类型
        if (count($typearr) > 0) {
            if (!in_array($fileInfo['type'], $typearr)) {
                die("文件上传失败！类型不符");
            }
        }

        //取后缀
        $ext  = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
        $path = VAR_PATH . "/uploads/";

        //生成随机文件名
        do {

            $newname = date("YmdHis") . rand(1000, 9999) . "." . $ext;

        } while (file_exists($path . $newname));

        //文件上传路径


        //判断是否上传成功
        if (is_uploaded_file($fileInfo['tmp_name'])) {
            if (move_uploaded_file($fileInfo['tmp_name'], $path . $newname)) {
                echo "上传成功！";
            } else {
                die("移动失败！");
            }

        } else {
            die("未知错误！请重试");

        }

    }
}
