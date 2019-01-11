<?php
/**
 *
 * @author      : wukun<charmfocus@gmail.com>
 * @copyright(c): 15-11-10
 * @version     : $id$
 */
class Q_Array
{
    /**
     * 深层对数据排序
     *
     * @param array $param
     * @param int   $sortFlags
     * @return bool
     */
    public static function ksortDeep(array &$param = array(), $sortFlags = SORT_REGULAR)
    {
        if (!$param) {
            return true;
        }

        ksort($param, $sortFlags);

        foreach ($param as &$_row) {
            if ($_row && is_array($_row)) {
                self::ksortDeep($_row);
            }
        }

        return true;
    }

    /**
     * 递归合并两个数组，键唯一
     *
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public static function arrayMergeRecursiveUnique(array $arr1, array $arr2)
    {
        foreach ($arr2 as $_k => &$_v) {
            if (is_array($_v) && isset($arr1[$_k]) && is_array($arr1[$_k])) {

                $arr1[$_k] = self::arrayMergeRecursiveUnique($arr1[$_k], $_v);
            } else {
                $arr1[$_k] = $_v;
            }
        }
        return $arr1;
    }

    /**
     * 以baseArr为基本进行数组合并,多余字段丢弃
     * @param array $baseArr
     * @param array $otherArr
     * @return array;
     */
    public static function arrayMergeBase(array $baseArr, array $otherArr)
    {
        foreach ($baseArr as $_key => &$_val) {
            if (isset($otherArr[$_key])) {
                $_val = $otherArr[$_key];
            }
        }
        return $baseArr;
    }

}