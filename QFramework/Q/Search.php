<?php

/**
 * Search Module
 * @package   Q_Search
 * @author    Rains <zhang.hongyin@zol.com.cn>
 * @copyright www.zol.com.cn
 * @version   1.0 $Id: Search.php 97 2009-05-06 03:26:24Z mojy $
 * @example
$search = new Q_Search(array('host'=>'blog.lucene.zol.com.cn'));
 * $keyword = '';
 * $start = 0;
 * $limit = 25;
 * $search->query(sprintf("select * from blog where nickname=%s limit %d, %d", $keyword, $start, $limit));
 * $results = $search->getResults();
 * $num_rows = $search->numRows();
 * var_dump($results);
 * var_dump($num_rows);
 */
/**
 * class Q_Search
 */
class Q_Search
{
    /**
     * the search server
     * @var string $host
     */
    private $host;

    /**
     * the port of search service
     * @var int $port
     */
    private $port = 6035;

    /**
     * the resource of the result data
     * @var resource $res
     */
    private $res;

    /**
     * the resource of the search connection
     * @var resource $link ;
     */
    private $link;

    /**
     * the seconds before connection timeout.
     * @var int $timeout
     */
    protected $timeout = 3;

    /**
     * encoding of the returned result.
     */
    protected $encoding = 'GBK';

    /**
     * construct function
     * accept some params
     * initialize the connection to the default search server.
     *
     * @param array $param
     * @throws Q_Exception
     */
    public function __construct($param = array())
    {
        /* the legal parameter list */
        $accept_param = array('host', 'port', 'timeout', 'encoding');
        foreach ($accept_param as $key) {
            if (isset($param[$key])) {
                $this->{$key} = $param[$key];
            }
        }
        /* has the host been set? */
        if ('' == $this->host) {
            throw new Q_Exception('$host has not been set yet!');
        }
        /* port range should be neither too low nor too high. */
        if ($this->port < 6035 || $this->port > 65535) {
            throw new Q_Exception('the $port value is invalid!');
        }
        /* now, connect it! */
        $this->connect($this->host, $this->port);
    }

    /**
     * Create a connection to the host
     * @param string $host
     * @param int    $port
     * @param int    $timeout
     * @return resource
     * @throws \Q_Exception
     */
    public function connect($host = '', $port = 6035, $timeout = 3)
    {
        if ('' == $host) {
            $host = $this->host;
        }
        if ($port < 1 || $port > 65535) {
            $port = $this->port;
        }
        if (!$timeout) {
            $timeout = $this->timeout;
        }
        $this->link = fsockopen($host, $port, $errno, $errstr, $timeout);
        if (!$this->link) {
            throw new Q_Exception("Connection failed! $errstr($errno)");
        }
        return $this->link;
    }

    /**
     * Query function
     * @param string $sql
     * @return bool
     * @throws \Q_Exception
     */
    function query($sql)
    {
        if ('' === $sql) {
            throw new Q_Exception('$sql should not be empty!');
        }
        if (false == $this->link) {
            $this->connect();
        }
        if (!fwrite($this->link, $sql . "\n")) {
            trigger_error('Send search query failed!', E_USER_WARNING);
            return false;
        }
        $result = '';
        while (($line = fgets($this->link, 4096)) !== false) {
            $result .= $line;
        }
        $doc = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (false === $doc) {
            trigger_error('Invalid document format!', E_USER_WARNING);
            return false;
        }
        $this->res = Q_String::u8conv($doc, $this->encoding);
        return !$this->res['@attributes']['state'];
    }

    /**
     * get search results array
     * @return array if there are results, or return false on error.
     */
    function getResults()
    {
        return isset($this->res['row']) ? $this->res['row'] : '';
    }

    /**
     * get the total num of the results.
     * @return int
     */
    function numRows()
    {
        return $this->res['@attributes']['hits'];
    }

    /**
     * close connection

     */
    function close()
    {
        fclose($this->link);
    }

    /**
     * free result

     */
    function free()
    {
        unset($this->res);
    }
}

