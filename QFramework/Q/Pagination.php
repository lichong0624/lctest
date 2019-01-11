<?php
/**
 * 强大的分页类
 * 具有模板功能
 *
 * @example
 *                $page = new Q_Pagination(array('total'=>500));
 *                echo $page->display(1);
 *                echo '<br/>' . $page->display('{PREV:[上一页]}{NEXT:[下一页]}');
 * @author        wiki
 * @copyright (c) 2009-4-1
 */
class Q_Pagination
{

    protected $_pageKey = 'page';
    /**
     * 模板
     *
     * @var string
     */
    protected $_template = '';

    /**
     * 当前页
     *
     * @var integer
     */
    protected $_page = 1;

    /**
     * 每页显示的数目
     *
     * @var integer
     */
    protected $_rowNum = 20;

    /**
     * 总数
     *
     * @var integer
     */
    protected $_total = 0;

    /**
     * 总页数
     */
    protected $_totalPage = 0;

    /**
     * 有没有页码Bar
     */
    protected $_existBar = false;

    /**
     * 当前页码焦点偏移量
     *
     * @var integer
     */
    protected $_offsetNum = 0;

    /**
     * 当面页码条中间显示宽
     *
     * @var integer
     */
    protected $_barLength = 0;

    protected $_url = '';


    protected $_target = '';

    /**
     * 系统标签
     */
    protected $_firstTag   = 'FIRST';
    protected $_prevTag    = 'PREV';
    protected $_prevHdTag  = 'PREVHD';#上一页的引导符
    protected $_barTag     = 'BAR';
    protected $_nextTag    = 'NEXT';
    protected $_nextHdTag  = 'NEXTHD';#下一页的引导符
    protected $_lasttTag   = 'LAST';
    protected $_currentTag = 'CURRENT';
    protected $_totalTag   = 'TOTAL';

    /**
     * 页码标签
     */
    protected $_numTag = '[NUM]';

    protected $_tagsVal = array();
    protected $_label   = array();
    /**
     * 标签分割符
     */
    protected $_tagDelimiter = '|';

    /**
     * 属性-值分割符
     */
    protected $_attrDelimiter = ':';

    /**
     * 处理模板表达式模板
     * {TAGS} = $this->_tag
     * {ATTRDELIMITER} = $this->_attrDelimiter
     */
    protected $_regexTemplate = '{(({TAGS}){ATTRDELIMITER}([^}]+))}';

    protected $_style = array(
        'FIRST'   => '',
        'PREV'    => '',
        'PREVhd'  => '',
        'BAR'     => '',
        'NEXT'    => '',
        'NEXThd'  => '',
        'LAST'    => '',
        'CURRENT' => 'current',
        'TOTAL'   => '',
    );

    #开始页码
    private $_startPage;

    #结束页码
    private $_endPage;

    private $_startOver = true;#开头

    private $_endOver = false;#结束

    /**
     * @param $option array('page'=>1, 'rownum'=>20, 'total'=>542, 'template'=>'');
     * @throws Q_Exception
     */
    public function __construct($option)
    {
        if (is_array($option)) {
            $this->_total = intval($option['total']);
            isset($option['rownum']) && ($this->_rowNum = intval($option['rownum']));
            isset($option['offsetnum']) && ($this->_offsetNum = intval($option['offsetnum']));
            isset($option['barlength']) && ($this->_barLength = intval($option['barlength']));
            isset($option['pagekey']) && ($this->_pageKey = $option['pagekey']);
            isset($option['template']) && ($this->_template = $option['template']);
            isset($option['target']) && ($this->_target = $option['target']);

            $page = isset($option['page']) ? (int)$option['page'] : 0;
            $url  = isset($option['url']) ? $option['url'] : '';
        } else {
            $this->_total = intval($option);
            $page         = 0;
            $url          = '';
        }

        if (empty($this->_total)) {
            throw new Q_Exception('total option is error!', -1);
        }

        $this->_setPage($page);
        $this->_setUrl($url);
        $this->_totalPage = ceil($this->_total / $this->_rowNum);
    }

    /**
     * 设置样式
     *
     * @param array $style
     * @return $this
     */
    public function setStyle(array $style)
    {
        if (!$style) {
            return $this;
        }
        $this->_style = array_merge($this->_style, $style);
        return $this;
    }

    public function get($var)
    {
        $var = '_' . $var;
        if (property_exists($this, $var)) {
            return $this->$var;
        } else {
            return null;
        }
    }

    /**
     * 设置属性值
     *
     * @param $var
     * @param $value
     * @return $this
     */
    public function set($var, $value)
    {
        $var = '_' . $var;
        if (property_exists($this, $var)) {
            $this->$var = $value;
        }

        return $this;
    }

    /**
     * 设置当前页
     *
     * @param $page
     */
    protected function _setPage($page)
    {
        $page = (int)$page;

        if ($page > 0) {
            $this->_page = $page;
        } else {
            $this->_page = (int)$_GET[$this->_pageKey];
        }
    }

    /**
     * 设置URL头
     *
     * @param string $url
     */
    protected function _setUrl($url = '')
    {
        if (strpos($url, '{PAGE}')) {
            $this->_url = $url;
        }

        $queryString = '';
        if (!empty($url)) {
            if (($offset = strpos($url, '?')) !== false) {
                $queryString = substr($url, $offset + 1);
                $url         = substr($url, 0, $offset);
            }
        } else {
            $queryString = empty($_SERVER['QUERY_STRING']) ? '' : $_SERVER['QUERY_STRING'];
            $url         = empty($_SERVER['PHP_SELF']) ? '' : $_SERVER['PHP_SELF'];
            if (!$queryString) {
                $url .= '?' . $this->_pageKey . '=';
            }
        }

        if ($queryString) {
            parse_str($queryString, $query);
            unset($query[$this->_pageKey]);
            if ($query) {
                $this->_url = $url . '?' . str_replace('&', '&amp;', http_build_query($query)) . '&amp;' . $this->_pageKey . '=';
            } else {
                $this->_url = $url . '?' . $this->_pageKey . '=';
            }
        } else {
            $this->_url = $url;
        }
    }

    /**
     * 解析模板
     *
     * @param string $code 模板内容
     * @return mixed
     */
    protected function parseTemplate($code = '')
    {
        $existBar = false;
        $label    = array();
        $code || ($code = $this->_template);
        $tags[] = $this->_firstTag;
        $tags[] = $this->_prevTag;
        $tags[] = $this->_prevHdTag;
        $tags[] = $this->_barTag;
        $tags[] = $this->_nextTag;
        $tags[] = $this->_nextHdTag;
        $tags[] = $this->_lasttTag;
        $regex  = str_replace(array('{TAGS}', '{ATTRDELIMITER}'),
            array(join('|', $tags), $this->_attrDelimiter),
            $this->_regexTemplate);
        preg_match_all("/{$regex}/s", $code, $result, PREG_SET_ORDER);

        $_tags = array();
        foreach ($result as $re) {
            $_tags[$re[2]] = $re[3];
            //处理中间分页条部分
            if ($re[3] && strpos($re[3], $this->_attrDelimiter) && $re[2] == $this->_barTag) {
                $_result       = explode($this->_attrDelimiter, $re[3]);
                $_tags[$re[2]] = $_result[0];

                $this->_barLength || ($this->_barLength = $_result[1]);
                $this->_offsetNum || ($this->_offsetNum = $_result[2]);
                $existBar = true;
            }
            $label[$re[2]] = $re[0];
        }
        $this->_existBar = $existBar;
        $this->_label    = $label;
        $this->_tagsVal  = $_tags;

        $this->interval();
        return $_tags;
    }

    /**
     * 页码条区间
     * @return bool
     */
    private function interval()
    {
        if (!$this->_existBar || $this->_barLength < 1) {
            return false;
        }

        if ($this->_barLength >= $this->_totalPage) {
            $start = 1;
            $end   = $this->_totalPage;
        } else {
            $start = $this->_page - $this->_offsetNum;
            $end   = $start + $this->_barLength;
            $end   = $start < 1 ? $this->_barLength : ($end - 1);

            $start = ($end > $this->_totalPage) ? ($start - ($end - $this->_totalPage)) : $start;

            $start = max($start, 1);
            $end   = min($end, $this->_totalPage);
        }

        $this->_startPage = $start;
        $this->_endPage   = $end;

        $this->_startOver = ($start == 1);
        $this->_endOver   = ($end == $this->_totalPage);
        return true;
    }

    /**
     * 第一页
     *
     * @param string $style
     * @return bool|string
     */
    public function firstTag($style = '')
    {
        if (empty($this->_tagsVal[$this->_firstTag])) {
            return false;
        }

        if ($this->_startOver) {
            return false;
        }

        $style = isset($this->_style['FIRST']) ? $this->_style['FIRST'] : $style;


        $num = 1;
        if ($this->_totalPage == 1) {
            return false;
        }
        $btn = $this->_getText($this->_firstTag, $num);

        if ($this->_page > 1) {
            return $this->_getLink($this->_getUrl($num), $btn, $style);
        }
        return $this->_getNoLink($btn, $style);
    }

    /**
     * 上一页
     *
     * @param string $style
     * @return bool|string
     */
    public function prevTag($style = '')
    {
        if (empty($this->_tagsVal[$this->_prevTag])) {
            return false;
        }

        $style = isset($this->_style['PREV']) ? $this->_style['PREV'] : $style;

        $num = $this->_page - 1;
        if ($this->_startOver) {
            return false;
        }
        $btn = $this->_getText($this->_prevTag, $num);
        if ($this->_page > 1) {
            return $this->_getLink($this->_getUrl($num), $btn, $style);
        }
        return $this->_getNoLink($btn, $style);
    }

    /**
     * 上一页引导符
     *
     * @param string $style
     * @return bool|string
     */
    public function prevhdTag($style = '')
    {
        if (empty($this->_tagsVal[$this->_prevHdTag])) {
            return false;
        }

        $style = isset($this->_style['PREVHD']) ? $this->_style['PREVHD'] : $style;

        $num = $this->_page - 1;
        if ($this->_startOver) {
            return false;
        }
        $btn = $this->_getText($this->_prevHdTag, $num);
        return $this->_getNoLink($btn, $style);
    }

    /**
     * 当前分页条
     *
     * @param string $style
     * @return string
     */
    public function barTag($style = '')
    {
        if (!$this->_existBar || $this->_barLength < 1) {
            return false;
        }

        $style = isset($this->_style['BAR']) ? $this->_style['BAR'] : $style;

        $this->interval();

        $bar = '';

        for ($i = $this->_startPage; $i <= $this->_endPage; $i++) {
            if ($i > $this->_totalPage) {
                break;
            } elseif ($i < 1) {
                continue;
            }
            $btn = $this->_getText($this->_barTag, $i);
            if ($i == $this->_page) {
                $style = isset($this->_style['CURRENT']) ? $this->_style['CURRENT'] : 'sel';
                $bar .= $this->_getNoLink($btn, $style);
            } else {
                $style = isset($this->_style['BAR']) ? $this->_style['BAR'] : '';
                $bar .= $this->_getLink($this->_getUrl($i), $btn, $style);
            }
        }
        return $bar;
    }

    /**
     * 下一页
     *
     * @param string $style
     * @return bool|string
     */
    public function nextTag($style = '')
    {
        if (empty($this->_tagsVal[$this->_nextTag])) {
            return false;
        }
        $num = $this->_page + 1;
        if ($this->_endOver) {
            return false;
        }

        $style = isset($this->_style['NEXT']) ? $this->_style['NEXT'] : $style;

        $btn = $this->_getText($this->_nextTag, $num);
        if ($this->_page < $this->_totalPage) {
            return $this->_getLink($this->_getUrl($num), $btn, $style);
        }
        return $this->_getNoLink($btn, $style);
    }

    /**
     * 下一页引导符
     *
     * @param string $style
     * @return bool|string
     */
    public function nexthdTag($style = '')
    {
        if (empty($this->_tagsVal[$this->_nextHdTag])) {
            return false;
        }

        $num = $this->_page + 1;
        if ($this->_endOver) {
            return false;
        }

        $style = isset($this->_style['NEXTHD']) ? $this->_style['NEXTHD'] : $style;

        $btn = $this->_getText($this->_nextHdTag, $num);
        return $this->_getNoLink($btn, $style);
    }


    /**
     * 最后一页
     *
     * @param string $style
     * @return bool|string
     */
    public function lastTag($style = '')
    {
        if (empty($this->_tagsVal[$this->_lasttTag])) {
            return false;
        }
        $num = $this->_totalPage;
        if ($this->_endOver) {
            return false;
        }

        $style = isset($this->_style['LAST']) ? $this->_style['LAST'] : $style;

        $btn = $this->_getText($this->_lasttTag, $num);
        if ($this->_page < $this->_totalPage) {
            return $this->_getLink($this->_getUrl($num), $btn, $style);
        }
        return $this->_getNoLink($btn, $style);
    }

    /**
     * 当前页
     *
     * @param string $style
     * @return bool|string
     */
    public function currentTag($style = '')
    {
        if (empty($this->_tagsVal[$this->_currentTag])) {
            return false;
        }

        $style = isset($this->_style['CURRENT']) ? $this->_style['CURRENT'] : $style;

        $num = $this->_page;
        $btn = $this->_getText($this->_currentTag, $num);
        return $this->_getNoLink($btn, $style);
    }


    public function pageInfo($style = '')
    {
        return '共<span class="' . $style . '">' . $this->_total . '</span>条 第<span class="' . $style . '">' . $this->_page . '</span>/' . $this->_totalPage . '页';
    }

    /**
     * 获取链接文字
     *
     * @param string     $tag  链接字符串
     * @param int|string $page 页码值
     * @return int|mixed|string
     */
    public function _getText($tag, $page = '')
    {
        return isset($this->_tagsVal[$tag])
            ? str_replace($this->_numTag, $page, $this->_tagsVal[$tag])
            : $page;
    }

    /**
     * 获取URL链接
     *
     * @param int $page
     * @return mixed|string
     */
    public function _getUrl($page = 1)
    {
        if (strpos($this->_url, '{PAGE}') !== false) {
            return str_replace('{PAGE}', $page, $this->_url);
        }

        return $this->_url . $page;
    }

    /**
     * 获取链接按钮
     *
     * @param        $url
     * @param        $text
     * @param string $style
     * @return string
     */
    public function _getLink($url, $text, $style = '')
    {
        $style = empty($style) ? '' : "class=\"{$style}\"";
        return '<a href="' . $url . '" ' . $style
               . ($this->_target ? (' target="' . $this->_target . '"') : '')
               . '>' . $text . '</a>';
    }

    public function _getNoLink($text, $style = '')
    {
        $style = empty($style) ? '' : "class=\"{$style}\"";
        return '<span ' . $style . '>' . $text . '</span>';
    }


    public function display($mixed = '')
    {
        $i    = 0;
        $code = '';
        if (is_string($mixed) && $mixed) {
            $code = $mixed;
        } elseif (is_integer($mixed)) {
            $i = $mixed;
        } else {
            $i = 1;
        }
        //默认几个
        if ($i > 0) {
            switch ($i) {
                case 1:
                    $code = '{FIRST:[NUM]...}{PREV:<}{BAR:[NUM]:10:3}{NEXT:>}{LAST:...[NUM]}';//DZ的
                    break;
                case 2:
                    $code = '{FIRST:[首页]}{PREV:[上页]}{NEXT:[下页]}{LAST:[尾页]}';//自定义的
                    break;
                case 3:
                    $code = '{PREV:[上一页]}{BAR:[[NUM]]:20:10}{NEXT:[下一页]}';//百度的
                    break;
                case 4 :
                    $code = '{PREV:&lt; 上一页}{PREVHD:...}{BAR:[NUM]:10:3}{NEXTHD:...}{NEXT:下一页 &gt;}{LAST:尾页}';//ZOL的
                    break;
                case 5 :
                    $code = '{PREV:上一页}{BAR:[NUM]:10:8}{NEXT:下一页}';//产品大全
                    break;
                case 6 :
                    $code = '{PREV: }{FIRST:[NUM]}{PREVHD:...}{BAR:[NUM]:10:2}{NEXTHD:...}{LAST:[NUM]}{NEXT: }';//列表页底部的
                    break;
                case 7 :
                    $code = '{PREV: }{BAR:[NUM]:-1:0}{NEXT: }';//列表页上部的
                    break;
            }
        }
        $this->parseTemplate($code);
        foreach ($this->_tagsVal as $tag => $val) {
            $func = strtolower($tag) . 'Tag';
            $code = str_replace($this->_label[$tag], $this->$func(), $code);
        }
        return $code;
    }
}
