<?php

class Q_Http
{
    const METHOD_GET     = 1;
    const METHOD_POST    = 2;
    const METHOD_PUT     = 4;
    const METHOD_DELETE  = 8;
    const METHOD_OPTIONS = 16;
    const METHOD_HEAD    = 32;

    /**
     * 发送header
     *
     * @param string|int $arg  header头信息,数字时,即为状态码
     * @param bool       $exit 发送header后是否中止程序
     */
    public static function sendHeader($arg, $exit = false)
    {
        //如果在cli模式下不发送header
        if (Q_Request::getRuntimeType() === Q_Request::CLI) {
            $exit && Q::end();
            return;
        }

        $header = '';
        if (is_string($arg)) {
            $header = self::_getContentType($arg);
            $header || header($arg);
        } elseif (is_int($arg)) {
            $header = self::_getStatusByCode($arg);
        }

        $header && header($header);
        $exit && Q::end();
    }

    protected static function _getContentType($type)
    {
        $types = array(
            'json'       => 'application/json',
            'html'       => 'text/html',
            'css'        => 'text/css',
            'js'         => 'text/javascript',
            'javascript' => 'text/javascript',
            'jpeg'       => 'image/jpeg',
            'pdf'        => 'application/pdf',
            'rss'        => 'application/rss+xml',
            'text'       => 'text/plain',
            'txt'        => 'text/plain',
            'xml'        => 'text/xml',
        );

        if (!empty($types[$type])) {
            return 'Content-Type:' . $types[$type];
        }
        return false;

    }

    protected static function _getStatusByCode($code)
    {
        $status = array(
            100 => "HTTP/1.1 100 Continue",
            101 => "HTTP/1.1 101 Switching Protocols",
            200 => "HTTP/1.1 200 OK",
            201 => "HTTP/1.1 201 Created",
            202 => "HTTP/1.1 202 Accepted",
            203 => "HTTP/1.1 203 Non-Authoritative Information",
            204 => "HTTP/1.1 204 No Content",
            205 => "HTTP/1.1 205 Reset Content",
            206 => "HTTP/1.1 206 Partial Content",
            300 => "HTTP/1.1 300 Multiple Choices",
            301 => "HTTP/1.1 301 Moved Permanently",
            302 => "HTTP/1.1 302 Found",
            303 => "HTTP/1.1 303 See Other",
            304 => "HTTP/1.1 304 Not Modified",
            305 => "HTTP/1.1 305 Use Proxy",
            307 => "HTTP/1.1 307 Temporary Redirect",
            400 => "HTTP/1.1 400 Bad Request",
            401 => "HTTP/1.1 401 Unauthorized",
            402 => "HTTP/1.1 402 Payment Required",
            403 => "HTTP/1.1 403 Forbidden",
            404 => "HTTP/1.1 404 Not Found",
            405 => "HTTP/1.1 405 Method Not Allowed",
            406 => "HTTP/1.1 406 Not Acceptable",
            407 => "HTTP/1.1 407 Proxy Authentication Required",
            408 => "HTTP/1.1 408 Request Time-out",
            409 => "HTTP/1.1 409 Conflict",
            410 => "HTTP/1.1 410 Gone",
            411 => "HTTP/1.1 411 Length Required",
            412 => "HTTP/1.1 412 Precondition Failed",
            413 => "HTTP/1.1 413 Request Entity Too Large",
            414 => "HTTP/1.1 414 Request-URI Too Large",
            415 => "HTTP/1.1 415 Unsupported Media Type",
            416 => "HTTP/1.1 416 Requested range not satisfiable",
            417 => "HTTP/1.1 417 Expectation Failed",
            500 => "HTTP/1.1 500 Internal Server Error",
            501 => "HTTP/1.1 501 Not Implemented",
            502 => "HTTP/1.1 502 Bad Gateway",
            503 => "HTTP/1.1 503 Service Unavailable",
            504 => "HTTP/1.1 504 Gateway Time-out",
        );
        if (!empty($status[$code])) {
            return $status[$code];
        }
        return false;
    }

    /**
     * @param int $time timestamp
     */
    public static function lastModified($time = SYSTEM_TIME)
    {
        header('Last-Modified:' . Q_Date::gmdateStr($time));
    }

    public static function expires($time = SYSTEM_TIME)
    {
        header('Expires:' . Q_Date::gmdateStr($time));
    }

    public static function maxAge($sec = 0)
    {
        header('Cache-Control: max-age=' . $sec);
    }

    /**
     * 设置过期时间
     *
     * @param integer $sec  秒
     * @param bool    $duly 是否正点过期
     */
    public static function setExpires($sec, $duly = false)
    {
        $lastModified = $duly ? (SYSTEM_TIME - (SYSTEM_TIME % $sec)) : (SYSTEM_TIME);
        $expireTime   = $lastModified + $sec;
        self::maxAge($sec);
        self::expires($expireTime);
        self::lastModified($lastModified);
    }

    /**
     * 设置不缓存页面
     *
     * @return void
     */
    public static function noCache()
    {
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
    }

    /**
     * 跳转
     *
     * @param        $url
     * @param string $msg
     * @param string $msgType
     */
    public static function redirect($url, $msg = '', $msgType = Q_TipMsg::MSG_TYPE_SUCCESS)
    {
        if (!empty($msg)) {
            Q_Cookie::set('_MSG', array('msg' => $msg, 'type' => $msgType));
        }
        header('location:' . $url);
        Q::end();
    }


    /**
     * 获取用户IP地址
     *
     * @return string
     */
    public static function userIp()
    {
        $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
        if (empty($ip)) {
            $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '';
        }
        if (empty($ip)) {
            $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        }
        return $ip;
    }

    /**
     * 获取主机名(域名)
     * 返回当前接受请求的域名
     *
     * @return string
     */
    public static function getHostName()
    {
        return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    }

    /**
     * 获取当前的协议
     *
     * @return string
     */
    public static function getProtocol()
    {
        return (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) ? 'https' : 'http';
    }

    /**
     * 获取当前URL地址
     * 返回当前的完整URL地址
     *
     * @return string
     */
    public static function currentUrl()
    {
        return self::getProtocol() . '://' . self::getHostName() . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
    }

    /**
     * 获取来路
     * 返回当前页面之前的页面地址。由于该信息是由客户端发送的，所以有可能为空。
     *
     * @return string
     */
    public static function getReferer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }

    /**
     * 获取用户代理信息
     * 返回客户端发送过来的user_agent信息。由于该信息是由客户端发送，所有有可能为空。
     *
     * @return string
     */
    public static function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    /**
     * 获取用户的请求方式
     *
     * @return string
     */
    public static function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 判断是否为POST请求
     *
     * @return boolean
     */
    public static function isPostRequest()
    {
        if (self::getMethod() == 'POST') {
            return true;
        }
        return false;
    }

    /**
     * 判断是否为GET请求
     *
     * @return boolean
     */
    public static function isGetRequest()
    {
        if (self::getMethod() == 'GET') {
            return true;
        }
        return false;
    }

    /**
     * 是否为AJAX请求
     *
     * @return boolean
     */
    public static function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * 获取用户请求时间
     * 返回用户发起请求到服务器的时间戳
     *
     * @return integer
     */
    public static function getRequestTime()
    {
        return isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
    }


    /**
     * POST数据
     *
     * @param string       $url
     * @param array|string $data
     * @param array        $files [field=>file]
     * @param array        $option
     * @return mixed
     */
    public static function post($url, $data, array $files = array(), array $option = array())
    {
        if (!empty($files)) {
            return self::postWithFile($url, $data, $files, $option);
        }

        if (is_array($data)) {
            $data = http_build_query($data);
        }

        $ch = curl_init();

        $_baseOption = array(
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => $data,

        );

        if (!empty($option)) {
            $option += $_baseOption;
        } else {
            $option = $_baseOption;
        }

        curl_setopt_array($ch, $option);

        $reply = curl_exec($ch);
        curl_close($ch);
        return $reply;
    }

    /**
     * 可上传文件的POST方式
     *
     * @param              $url
     * @param array|string $data
     * @param array        $files
     * @param array        $option
     * @return mixed
     */
    public static function postWithFile($url, $data, array $files, array $option = array())
    {
        if (is_array($data)) {
            $data = http_build_query($data);
        }

        static $disallow = array("\0", "\"", "\r", "\n");

        $payload = explode('&', urldecode($data));

        $boundary  = '---------------------qf' . uniqid();
        $_boundary = '--' . $boundary;

        $body = array();

        foreach ($payload as $_row) {
            $_row = explode('=', $_row);
            $k    = str_replace($disallow, "_", $_row[0]);
            $v    = filter_var($_row[1]);

            $body[] = $_boundary;
            $body[] = "Content-Disposition: form-data; name=\"{$k}\"";
            $body[] = '';
            $body[] = "{$v}";
        }

        $files = explode('&', urldecode(http_build_query($files)));

        foreach ($files as $_row) {
            list($k, $v) = explode('=', $_row);

            switch (true) {
                case false === $v = realpath(filter_var($v)):
                case !is_file($v):
                case !is_readable($v):
                    continue;
            }

            $_file = file_get_contents($v);

            $k = str_replace($disallow, "_", $k);
            $v = explode(DIRECTORY_SEPARATOR, str_replace($disallow, "_", $v));
            $v = end($v);

            $body[] = $_boundary;
            $body[] = implode("\r\n", array(
                "Content-Disposition: form-data; name=\"{$k}\"; filename=\"{$v}\"",
                "Content-Type: application/octet-stream",
                "",
                $_file,
            ));
        }

        $body[] = "--{$boundary}--";
        $body[] = '';

        $body = join("\r\n", $body);

        $ch = curl_init();

        empty($option) || curl_setopt_array($ch, $option);


        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => array(
                'Expect: 100-continue',
                'Content-Type: multipart/form-data; boundary=' . $boundary,
            ),
        ));

        $reply = curl_exec($ch);
        curl_close($ch);
        return $reply;
    }
}
