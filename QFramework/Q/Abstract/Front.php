<?php

/*
|---------------------------------------------------------------
| 控制器
|---------------------------------------------------------------
| @package Q
|
*/
abstract class Q_Abstract_Front
{
    const RETURN_NOT        = false;
    const RETURN_TYPE_HTML  = 'HTML';
    const RETURN_TYPE_JSON  = 'JSON';
    const RETURN_TYPE_ARRAY = 'ARRAY';


    /**
     * 运行应用
     *
     * @param Q_Request|null  $request
     * @param Q_Response|null $response
     * @param bool|false      $flush
     * @param mixed           $return {STRING|JSON|ARRAY}
     * @return string|mixed
     * @throws Q_Exception
     */
    public static function run(Q_Request $request = null, Q_Response $response = null, $flush = false, $return = self::RETURN_NOT)
    {
        //如果外部传入request对象，说明是由程序运行run方法的，设置其运行时环境为NATIVE
        if ($request) {
            Q_Request::setRuntimeType(Q_Request::NATIVE);
        }

        $request || $request = Q_Request::instance();
        $response || $response = Q_Response::instance();

        //注册为全局可用
        Q_Registry::set('Q_Request', $request);
        Q_Registry::set('Q_Response', $response);

        $action = ucfirst($request->actName);
        if (empty($action)) {
            throw new Q_Exception("The action of '$action' is empty in request!");
        }

        //        $ctlManager = APP_NAME . '_Front';
        //        if (Q::classExists($ctlManager)) {
        //            return $ctlManager::run($request, $response, $flush, $return);
        //        }

        /**
         * @var Q_Interface_UserFront $_customMgr
         */
        $_customMgr = APP_NAME . '_Front';

        if (Q::classExists($_customMgr)) {
            $_customMgr::prepare($request, $response);
        }

        /**
         * @var Q_Abstract_Page $page
         */
        $page           = static::_getPage($request, $response);
        $page->request  = $request;
        $page->response = $response;

        $_expire = $page->getExpire();

        if (!$flush && $_expire !== false && $html = $page->getCache()) {
            if ($return) {
                return static::_convHtmlData($html, $return);
            }

            die($html);
        }

        $return && ob_start();

        if (!$page->validate($request, $response)) {
            throw new Q_Exception('validate failed!');
        }


        $_act   = $action;
        $action = 'do' . $_act;
        $exec   = 'exec' . $_act;
        if (method_exists($page, $action)) {
            if ($request->execName) {
                $exec = method_exists($page, $exec) ? $exec : (method_exists($page, 'exec') ? 'exec' : '');
                if ($exec) {
                    $page->$exec($request, $response);//执行提交
                }
            }

            $page->$action($request, $response);
        } else {
            $controller = get_class($page);
            throw new Q_Exception("The function of '{$action}' does not exist in class '$controller'!");
        }

        //执行公共方法
        if (method_exists($page, 'display')) {
            $page->display($request, $response);
        }

        $_html = $response->display();
        $_expire !== false && $page->setCache($_html);

        if ($return) {
            return static::_convHtmlData(ob_get_clean(), $return);
        }
    }

    protected static function _convHtmlData($html, $type = self::RETURN_TYPE_HTML)
    {
        switch ($type) {
            case self::RETURN_TYPE_JSON:
            case self::RETURN_TYPE_ARRAY:
                $html = json_decode($html, true);
                break;
        }
        return $html;
    }

    /**
     * @param Q_Request  $request
     * @param Q_Response $response
     * @return Q_Abstract_Page
     * @throws Q_Exception
     */
    protected static function _getPage(Q_Request $request, Q_Response $response)
    {
        $controller = $request->ctlName;
        $action     = $request->actName;
        $version    = $request->verName;

        $hasVer = false;

        $verConf = self::_getVerConf($version, $_prefixVer, $_suffixVer);

	if ($version && $verConf) {
            $_ctlClass = static::_getCtlClassName($controller, $version);

            if (isset($verConf[$_suffixVer])
                && $verConf[$_suffixVer] == true
                && Q::classExists($_ctlClass)
            ) {//存在
                $hasVer = true;
            } else {
                $_maxMatchVer = null;
                foreach ($verConf as $_ver => $_has) {
                    if (!$_has) {
                        continue;
                    }

                    $_ver = strtolower($_ver[0]) === 'v' ? strtolower($_ver) : strtolower('v' . $_ver);
                    if (version_compare($_suffixVer, $_ver, '<')) {
                        continue;
                    }

                    $_version = $_prefixVer ? ($_prefixVer . '_' . $_ver) : $_ver;
                    $_ctlClass = static::_getCtlClassName($controller, $_version);
                    if (!Q::classExists($_ctlClass)) {
                        continue;
                    }

                    if (version_compare($_maxMatchVer, $_ver, '<')) {
                        $_maxMatchVer = $_ver;
                    }
                }

                if ($_maxMatchVer) {
                    $_suffixVer = $_maxMatchVer;
                    $hasVer     = true;
                }
            }

            if ($hasVer) {
                $version    = $_prefixVer ? ($_prefixVer . '_' . $_suffixVer) : $_suffixVer;
                $controller = strtr($version, '.', '_') . '_' . $controller;
                $request->setCurVerName($version);
                $response->setVerName($version)
                    ->setCurVerName($version);
            }
        }

        $response->setCtlName($controller)->setActName($action);
        $ctlClass = static::_getCtlClassName($controller);

        if (!Q::classExists($ctlClass)) {
            if (false !== strpos($ctlClass, '_')) {
                $ctlClass = strtr($ctlClass, '_', '\\');
                if (!Q::classExists($ctlClass)) {
                    throw new Q_Exception("The controller of '{$ctlClass}' is not exists!");
                }
            } else {
                throw new Q_Exception("The controller of '{$ctlClass}' is not exists!");
            }
        }

        $page           = new $ctlClass($request, $response);
        $page->request  = $request;
        $page->response = $response;

        return $page;
    }

    protected static function _getVerConf($version, &$prefixVer = null, &$suffixVer = null)
    {
        $verConfList = Q_Config::get(APP_NAME . '_Version', 'LIST');

        if (empty($verConfList)) {
            return null;
        }

        if ($pos = strrpos($version, '_')) {
            $_vers     = explode('_', $version);
            $suffixVer = array_pop($_vers);
            $prefixVer = substr($version, 0, $pos);

            $def = empty($verConfList['*']) ? null : $verConfList['*'];
            foreach ($_vers as $_ver) {
                $_ver = strtoupper($_ver);
                if (empty($verConfList[$_ver])) {
                    return $def;
                }
                $verConfList = $verConfList[$_ver];
                $def         = empty($verConfList['*']) ? $def : $verConfList['*'];
            }
        } else {
            $suffixVer   = $version;
            $verConfList = empty($verConfList['*']) ? $verConfList : $verConfList['*'];
        }

        return $verConfList;

    }

    protected static function _getCtlClassName($controller, $version = '')
    {
        $_name = APP_NAME . '_Page_';
        $_name .= $version ? (strtr($version, '.', '_') . '_') : '';
        $_name .= $controller;
        return $_name;
    }
}


