<?php

class Admin_Page_Class extends Admin_Page_Abstract
{
    /*
     * 分类列表
     * */
    public function doIndex(Q_Request $input, Q_Response $output){
//        Q::debug(self::$_ctlName,false);
//        Q::debug(self::$_actName);
        $data = Q_DAL_Client::instance()->call('Class', 'Gets',['order' => 'id ASC']);
        $output->data = $data;
        $output->clearLayout()->setTemplate('~/Index');
    }

    /*
     * 添加分类
     * */
    public function doAdd(Q_Request $input, Q_Response $output){
        $output->clearLayout()->setTemplate();
    }

    public function doAddData(Q_Request $input, Q_Response $output){

        $data = $input->postArray();
        if (empty($data)&&!isset($data)) {
            self::showMsg('请填写分类', self::STATUS_ERROR, 2, '/?c=Class&a=Add');
        }
        $where = [
            'where' => [
               'class_name' => $data['class_name']
            ]
        ];
        $_state = Q_DAL_Client::instance()->call('Class', 'GetRow', $where);

        if ($_state) {
            self::showMsg('分类已存在', self::STATUS_ERROR, 2, '/?c=Class&a=Add');
        }
        $_res = Q_DAL_Client::instance()->call('Class', 'Save', $data);

        if ($_res) {
            self::showMsg('保存成功', self::STATUS_ERROR, 2, '/?c=Class&a=Index');
        }
    }

    /*
     * 编辑分类
     * */
    public function doEditClass(Q_Request $input, Q_Response $output){
        if (Q_Http::isGetRequest()) {
           $_id = $input -> get('id');

                if (!$_id) {
                    self::showMsg('网络异常,稍后再试', self::STATUS_ERROR, 2, '/?c=Class&a=Index');
                }
                $where = [
                    'where' => [
                        'id' => $_id
                    ]
                ];
                $_state = Q_DAL_Client::instance()->call('Class', 'GetRow', $where);

                $output->id    = $_id;
                $output->class = $_state['class_name'];
                $output->clearLayout()->setTemplate();
        }
        if (Q_Http::isPostRequest()) {
            $data = $input-> postArray();

            $where = [
                'where' => [
                    'class_name' => $data['class_name']
                ]
            ];
            $_state = Q_DAL_Client::instance()->call('Class', 'GetRow', $where);
            if ($_state) {
                self::showMsg('分类已存在', self::STATUS_ERROR, 2, '/?c=Class&a=EditClass&id='.$data['id']);
            }

            $_res = Q_DAL_Client::instance()->call('Class', 'UpdateByWhere', $data);
            if ($_res) {
                self::showMsg('编辑成功', self::STATUS_ERROR, 2, '/?c=Class&a=Index');
            }

            $output->clearLayout()->setTemplate();
        }

    }

    /*
     * 删除
     * */

    public function doDeleClass(Q_Request $input, Q_Response $output){
        $_id = $input->get('id');
        if (!$_id) {
            self::showMsg('网络异常,稍后再试', self::STATUS_ERROR, 2, '/?c=Class&a=Index');
        }
        $data = [
            'id' => $_id
        ];
        $res = Q_DAL_Client::instance()->call('Class', 'Delete', $data);

        if (!$res) {
            self::showMsg('删除失败', self::STATUS_ERROR, 2, '/?c=Class&a=Index');
        }
        //  2  跳转时间
        self::showMsg('删除成功', self::STATUS_SUCCESS, 2, '/?c=Class&a=Index');
    }
}
