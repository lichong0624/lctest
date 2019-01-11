<?php

class Q_Exception extends Exception
{
    protected $trace;

    /**
     * 修正code，支持string
     *
     * @var int|string
     */
    protected      $_code;
    private static $errorLevels = array(
        E_ERROR           => 'Error',
        E_WARNING         => 'Warning',
        E_PARSE           => 'Parsing Error',
        E_NOTICE          => 'Notice',
        E_CORE_ERROR      => 'Core Error',
        E_CORE_WARNING    => 'Core Warning',
        E_COMPILE_ERROR   => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR      => 'User Error',
        E_USER_WARNING    => 'User Warning',
        E_USER_NOTICE     => 'User Notice',
        E_STRICT          => 'Runtime Notice'
    );

    private $_isFromError = false;

    /**
     * @return boolean
     */
    public function isFromError()
    {
        return $this->_isFromError;
    }

    /**
     * @param boolean $isFromError
     * @return $this
     */
    public function setIsFromError($isFromError)
    {
        $this->_isFromError = $isFromError;
        return $this;
    }

    public function __construct($errorMsg = '', $code = 0, $file = '', $line = 0, $trace = null)
    {
        $this->_code = $code;
        $code        = (int)$code;
        parent::__construct($errorMsg, $code);
        if (!empty($file)) {
            $this->file = $file;
        }
        if (!empty($line)) {
            $this->line = $line;
        }

        if (!empty($trace)) {
            $this->trace = $trace;
        } else {
            $this->trace = $this->getTrace();
        }
    }

    public function __toString()
    {
        $trace = $this->trace;
        krsort($trace);

        $string = "<br />\n<h2>Stack trace:</h2>\n";

        $type = $this->isFromError()
            ? (isset(self::$errorLevels[$this->_code]) ? self::$errorLevels[$this->_code] : $this->_code)
            : $this->_code;

        $_name  = empty($trace[0]['class']) ? "function '<b>{$trace[0]['function']}</b>'" : "class '<b>{$trace[0]['class']}</b>'";
        $string .= "Exception {$_name}<br />\n" .
                   '<b>TIME:</b> ' . SYSTEM_DATE . "<br />\n" .
                   '<b>MESSAGE:</b> ' . $this->message . "<br />\n" .
                   '<b>TYPE:</b> ' . $type . "<br />\n" .
                   '<b>FILE:</b> ' . $this->file . "<br />\n" .
                   '<b>LINE:</b> ' . $this->line . "<br />\n";
        $rowNum = 1;
        if (!empty($trace)) {
            foreach ($trace as $key => $val) {
                if ($key == 0) {
                    continue;
                }
                $args = array();
                if (!empty($val['args'])) {
                    foreach ($val['args'] as $v) {
                        $args[] = is_object($v) ? (sprintf('Object(%s)', get_class($v)))
                            : (is_array($v) ? gettype($v) : "'$v'");
                    }
                }
                $args         = implode(', ', $args);
                $val['class'] = isset($val['class']) ? $val['class'] : '';
                $val['type']  = isset($val['type']) ? $val['type'] : '';
                $val['file']  = isset($val['file']) ? $val['file'] : '';
                $val['line']  = isset($val['line']) ? "($val[line]):<br />\n" : '';
                $string       .= "#$rowNum $val[file]$val[line]<b>$val[class]$val[type]$val[function]($args) </b><br /> <br />\n";
                ++$rowNum;
            }
        }
        $string .= $this->getDebugInfo();

        return $string;
    }

    protected function getDebugInfo()
    {
        $ret          = "<h2>Debug Info:</h2>\n";
        $contentLines = file($this->file);
        $total        = count($contentLines);
        $startLine    = ($this->line < 5) ? 0 : ($this->line - 5);
        $endLine      = $this->line + 5;
        $endLine      = ($total >= $endLine) ? $endLine : $total;

        for ($i = $startLine; $i < $endLine; ++$i) {
            if ($i == ($this->line - 1)) {
                $ret .= '<font color="red"><b> >>' . ($i + 1) . ' ' . htmlspecialchars($contentLines[$i]) . "</b></font><br />\n";
            } else {
                $ret .= '<b>' . ($i + 1) . '</b> ' . htmlspecialchars($contentLines[$i]) . "<br />\n";
            }
        }
        return $ret;
    }

    public static function register()
    {
        set_exception_handler(array(__CLASS__, 'handler'));
    }

    public static function handler($exception)
    {

        if ($exception instanceof Exception || $exception instanceof Throwable) {
            $exception = new Q_Exception($exception->getMessage(), $exception->getCode(), $exception->getFile(), $exception->getLine(), $exception->getTrace());
        }

        if ($exception instanceof Exception) {
            $debugging  = defined('IS_DEBUGGING') ? IS_DEBUGGING : false;
            $production = defined('IS_PRODUCTION') ? IS_PRODUCTION : false;

            Q_Http::sendHeader(500);
            if ($production) {
                Q_Log::write(Q_String::clean($exception), Q_Log::TYPE_EXCEPTION);
            }

            if ($debugging) {
                echo (Q_Request::resolveType() == Q_Request::CLI)
                    ? Q_String::clean($exception)
                    : $exception;
            }
        }
    }
}


