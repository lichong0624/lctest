<?php

/**
 * @author        wiki <charmfocus@gmail.com>
 * @copyright(c)  14-10-5
 */
abstract class Q_DAL_Server extends Q_Abstract_Page
{
    protected $_module = null;
    protected $_method = null;
    protected $_param  = null;
    protected $_expire = null;
    protected $_flush  = false;
    protected $_appId  = 0;
    protected $_appKey = null;
    protected $_worker = Q_DAL_Client::WORKER_CALL;
    protected $_sign   = null;
    protected $_files  = null;
    protected $_conf;

    const DATA_STATUS_ERROR   = 0;
    const DATA_STATUS_SUCCESS = 1;


    public function validate(Q_Request $input, Q_Response $output)
    {
        //只针对default方法进行验证
        if (strtolower($input->getActionName()) != 'default') {
            return true;
        }

        $this->_conf = Q_Config::get(array('DAL', APP_NAME . '_DAL'));

        $module = $input->post(Q_DAL_Client::POST_KEY_MODULE);
        $method = $input->post(Q_DAL_Client::POST_KEY_METHOD);
        $param  = $input->postArray(Q_DAL_Client::POST_KEY_PARAM, null, true);
        $expire = $input->post(Q_DAL_Client::POST_KEY_EXPIRE);
        $flush  = $input->postBool(Q_DAL_Client::POST_KEY_FLUSH, 0);
        $appId  = $input->post(Q_DAL_Client::POST_KEY_APPID);
        $worker = $input->post(Q_DAL_Client::POST_KEY_WORKER, Q_DAL_Client::WORKER_CALL);
        $sign   = $input->post(Q_DAL_Client::POST_KEY_SIGN);
        $files  = $input->files();

        $module = Q_Inflector::formatName($module);
        $method = Q_Inflector::formatName($method);

        if (is_string($module) && strpos($module, '/')) {
            $_module = explode('/', $module);
            $module  = $_module[0];
            $method  = $_module[1];
        }


        $this->_module = $module;
        $this->_method = $method;
        $this->_param  = Q_String::clean($param);
        $this->_expire = $expire;
        $this->_flush  = $flush;
        $this->_appId  = $appId;
        $this->_appKey = $this->getAppKey();
        $this->_worker = $worker ? $worker : Q_DAL_Client::WORKER_CALL;
        $this->_sign   = $sign;
        $this->_files  = $files;

        try {
            if (!$this->_appKey) {
                throw new Q_Exception('APP_KEY is empty!', -100);
            }

            //TODO:权限问题等之后再加
            if (!$this->checkPower()) {
                throw new Q_Exception('Access denied!', -101);
            }

            //如果没有配置CHECK_SIGN或CHECK——SIGN设置为TRUE，需要对签名进行验证
            if (!isset($this->_conf['CHECK_SIGN']) || $this->_conf['CHECK_SIGN']) {
                $ckData = array(
                    Q_DAL_Client::POST_KEY_MODULE => $module,
                    Q_DAL_Client::POST_KEY_METHOD => $method,
                    Q_DAL_Client::POST_KEY_PARAM  => $param,
                    Q_DAL_Client::POST_KEY_FLUSH  => $flush,
                    Q_DAL_Client::POST_KEY_APPID  => $appId,
                    Q_DAL_Client::POST_KEY_APPKEY => $this->_appKey,
                    Q_DAL_Client::POST_KEY_WORKER => $worker,
                );

                $expire && $ckData[Q_DAL_Client::POST_KEY_EXPIRE] = $expire;

                Q_Array::ksortDeep($ckData);

                $ckSign = md5(urldecode(http_build_query($ckData)));
                
                if ($ckSign != $this->_sign) {
                    throw new Q_Exception("{$module} signature failed to verify!", -102);
                }
                return ($ckSign === $this->_sign);
            }

            return true;

        } catch (Q_Exception $ex) {
            $output->jsonReturn(null, $ex->getCode(), $ex->getMessage());
        }

        return false;
    }

    public function doDefault(Q_Request $input, Q_Response $output)
    {
        $_data   = null;
        $_msg    = '';
        $_status = self::DATA_STATUS_SUCCESS;

        try {
            if ($this->_worker == Q_DAL_Client::WORKER_CALL) {
                //当有自定义PAGE时，调用自定义PAGE
                if (method_exists(APP_NAME . '_Page_' . ucfirst($this->_module), 'do' . $this->_method)) {
                    $input->removeSourceData(Q_Request::METHOD_POST)
                        ->setControllerName($this->_module)
                        ->setActionName($this->_method)
                        ->add($this->_param, Q_Request::METHOD_POST);
                    $_data = Q_Controller_Front::run($input, $output, false, true);
                } else {
                    $_data = Q_DAL_Client::instance()->call($this->_module, $this->_method, $this->_param, $this->_files, $this->_flush, Q_DAL_Client::MODE_NATIVE);
                }
            } else if ($this->_worker == Q_DAL_Client::WORKER_CLEAR) {
                $_data = Q_DAL_Client::instance()->clearCache($this->_module, $this->_method, $this->_param, $this->_expire, $this->_flush, Q_DAL_Client::MODE_NATIVE);
            } else {
                throw new Q_Exception('the worker type is error!');
            }
        } catch (Q_Exception $ex) {
            $_data   = null;
            $_status = $ex->getCode();
            $_msg    = $ex->getMessage();
        }

        $output->jsonReturn($_data, $_status, $_msg);
    }

    /**
     * 通过APPID获取APPKEY
     *
     * @return string
     */
    abstract public function getAppKey();

    /**
     * 权限验证需要重写
     *
     * @return bool
     */
    public function checkPower()
    {
        return true;
    }
}