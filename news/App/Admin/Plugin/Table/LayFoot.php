<?php
/**
 *
 * @author      : arvin<huxudong@dalingpao.com>
 * @copyright(c): 17-10-25
 * @version     : $id$
 */
class Admin_Plugin_Table_LayFoot extends Admin_Plugin_Abstract
{
    const COLS_TYPE_NORMAL   = 'normal';
    const COLS_TYPE_CHECKBOX = 'checkbox';
    const COLS_TYPE_SPACE    = 'space';
    const COLS_TYPE_NUMBERS  = 'numbers';

    const COLS_MIN_WIDTH = 60;

    const COLS_FIXED_LEFT  = 'left';
    const COLS_FIXED_RIGHT = 'right';

    const COLS_ALIGN_LEFT   = 'left';
    const COLS_ALIGN_CENTER = 'center';
    const COLS_ALIGN_RIGHT  = 'right';

    protected static $_instance = null;
    protected        $_foot     = [];

    private $_field    = '';//String
    private $_title    = '';//String
    private $_width    = '';//Number/String
    private $_minWidth = self::COLS_MIN_WIDTH;//Number
    private $_type     = self::COLS_TYPE_NORMAL;//String
    private $_fixed    = false;//String
    private $_unresize = true;//Boolean
    private $_style    = '';//String
    private $_align    = self::COLS_ALIGN_LEFT;//String
    private $_colspan  = 1;//Number
    private $_rowspan  = 1;//Number
    private $_templet  = '';//String
    private $_toolbar  = '';//String

    public function __construct(array $colsConfig = [])
    {
        $defCol = [
            'field'    => $this->_field,
            'value'    => $this->_title,
            'width'    => $this->_width,
            'minWidth' => $this->_minWidth,
            'type'     => $this->_type,
            'fixed'    => $this->_fixed,
            'unresize' => $this->_unresize,
            'style'    => $this->_style,
            'align'    => $this->_align,
            'colspan'  => $this->_colspan,
            'rowspan'  => $this->_rowspan,
            'templet'  => $this->_templet,
            'toolbar'  => $this->_toolbar,
        ];

        $cols = [];
        foreach ($colsConfig as $_field => $_row) {
            if (!isset($_row['field'])) {
                $_row['field'] = $_field;
            }

            $cols[$_field] = array_merge($defCol, $_row);
        }

        $this->setFoot($cols);
    }

    /**
     * @param array $footConfig
     * @return Admin_Plugin_Table_LayFoot
     */
    public static function instance(array $footConfig = [])
    {
        $cacheKey = md5(json_encode($footConfig));

        if (isset(self::$_instance[$cacheKey])) {
            $obj = self::$_instance[$cacheKey];
        } else {
            $obj                        = new self($footConfig);
            self::$_instance[$cacheKey] = $obj;
        }

        return $obj;
    }

    /**
     * @return array
     */
    public function getFoot(): array
    {
        return $this->_foot;
    }

    /**
     * @param array $foot
     */
    public function setFoot(array $foot)
    {
        $this->_foot = $foot;
    }
}