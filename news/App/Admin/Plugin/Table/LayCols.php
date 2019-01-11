<?php
/**
 *
 * @author      : arvin<huxudong@dalingpao.com>
 * @copyright(c): 17-10-25
 * @version     : $id$
 */
class Admin_Plugin_Table_LayCols extends Admin_Plugin_Abstract
{
    const COLS_TYPE_NORMAL   = 'normal';
    const COLS_TYPE_CHECKBOX = 'checkbox';
    const COLS_TYPE_SPACE    = 'space';
    const COLS_TYPE_NUMBERS  = 'numbers';

    const COLS_MIN_WIDTH    = 60;
    const LAY_CHECKED_FALSE = false;
    const LAY_CHECKED_TRUE  = true;

    const COLS_FIXED_LEFT  = 'left';
    const COLS_FIXED_RIGHT = 'right';


    const COLS_EDIT_TYPE_TEXT = 'text';

    const COLS_ALIGN_LEFT   = 'left';
    const COLS_ALIGN_CENTER = 'center';
    const COLS_ALIGN_RIGHT  = 'right';

    const COL_CHECKED_FIELD = 'LAY_CHECKED';

    protected static $_instance = null;
    protected        $_cols     = [];

    private $_field       = '';//String
    private $_title       = '';//String
    private $_width       = '';//Number/String
    private $_minWidth    = self::COLS_MIN_WIDTH;//Number
    private $_type        = self::COLS_TYPE_NORMAL;//String
    private $_LAY_CHECKED = self::LAY_CHECKED_FALSE;//Boolean
    private $_fixed       = false;//String
    private $_sort        = false;//Boolean
    private $_unresize    = true;//Boolean
    private $_edit        = false;//String
    private $_event       = '';//String
    private $_style       = '';//String
    private $_align       = self::COLS_ALIGN_LEFT;//String
    private $_colspan     = 1;//Number
    private $_rowspan     = 1;//Number
    private $_templet     = '';//String
    private $_toolbar     = '';//String


    public function __construct(array $colsConfig = [])
    {
        $defCol = [
            'field'       => $this->_field,
            'title'       => $this->_title,
            'width'       => $this->_width,
            'minWidth'    => $this->_minWidth,
            'type'        => $this->_type,
            'LAY_CHECKED' => $this->_LAY_CHECKED,
            'fixed'       => $this->_fixed,
            'sort'        => $this->_sort,
            'unresize'    => $this->_unresize,
            'edit'        => $this->_edit,
            'event'       => $this->_event,
            'style'       => $this->_style,
            'align'       => $this->_align,
            'colspan'     => $this->_colspan,
            'rowspan'     => $this->_rowspan,
            'templet'     => $this->_templet,
            'toolbar'     => $this->_toolbar,
        ];

        $cols = [];
        foreach ($colsConfig as $_field => $_row) {
            if (!isset($_row['field'])) {
                $_row['field'] = $_field;
            }

            if (!isset($_row['title'])) {
                if (isset($_row['name'])) {
                    $_row['title'] = $_row['name'];
                }
            }

            $cols[$_field] = array_merge($defCol, $_row);
        }

        $this->setCols($cols);
    }

    /**
     * @param array $colsConfig
     * @return Admin_Plugin_Table_LayCols
     */
    public static function instance(array $colsConfig = [])
    {
        $cacheKey = md5(json_encode($colsConfig));

        if (isset(self::$_instance[$cacheKey])) {
            $obj = self::$_instance[$cacheKey];
        } else {
            $obj                        = new self($colsConfig);
            self::$_instance[$cacheKey] = $obj;
        }

        return $obj;
    }

    /**
     * @return array
     */
    public function getCols(): array
    {
        return $this->_cols;
    }

    /**
     * @param array $cols
     */
    public function setCols(array $cols)
    {
        $this->_cols = $cols;
    }
}