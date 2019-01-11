<?php

/**
 *
 * 菜单插件类
 *
 * @author       hanqiang <123456@qq.com>
 * @copyright(c) 2016-07-14 16:03:51
 */
class Admin_Plugin_Menu extends Admin_Plugin_Abstract
{
    protected static $_cache = array();


    /**
     * 获取菜单列表
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return bool|string
     */
    public static function getList(Q_Request $input, Q_Response $output)
    {
        $data = $input->data;

        if (empty($data)) {
            return false;
        }
        $html = '';
        foreach ($data as $_row) {
            if (!empty($_row['son'])) {
                $input->data   = $_row['son'];
                $output->child = self::getList($input, $output);
            } else {
                $output->child = null;
            }
            $output->_row = $_row;

            $html .= $output->fetchCol('Plugin/Menu/MenuItem');

        }
        return $html;
    }

    /**
     * 获取导航栏菜单
     *
     * @param Q_Request  $input
     * @param Q_Response $output
     * @return string
     * @throws Q_Exception
     */
    public static function getSideBar(Q_Request $input, Q_Response $output)
    {
        $sidebarNavHtml = '';
        $menuData       = Q_DAL_Client::instance()->call('Admin_' . APP_NAME . 'Menu', 'GetsByGroup', ['admin_id' => $input->adminId]);

        if (empty($menuData)) {
            return $sidebarNavHtml;
        }

        $curMenuId = self::getCurMenuId();
        foreach ($menuData as &$menu) {
            if ($menu['is_hidden']) {
                continue;
            }

            //处理sidebarMenu
            if (!empty($menu['son'])) {
                $menu['itemHtml'] = '';
                foreach ($menu['son'] as &$_subMenu) {
                    if ($_subMenu['is_hidden']) {
                        continue;
                    }

                    if (!empty($_subMenu['son'])) {
                        $_subMenu['childHtml'] = '';
                        foreach ($_subMenu['son'] as &$_childMenu) {
                            if ($_childMenu['is_hidden']) {
                                continue;
                            }
                            $_subMenu['childHtml'] .= $output->fetchCol('Plugin/Menu/SidebarMenuSubItem', ['menu' => $_childMenu, 'curMenuId' => $curMenuId]);
                        }
                    }

                    $menu['itemHtml'] .= $output->fetchCol('Plugin/Menu/SidebarMenuItem', ['menu' => $_subMenu, 'curMenuId' => $curMenuId]);
                }
            }

            $sidebarNavHtml .= $output->fetchCol('Plugin/Menu/SidebarMenu', ['menu' => $menu, 'curMenuId' => $curMenuId]);
        }

        return $output->fetchCol('Plugin/Sidebar/Sidebar', ['sideBar' => $sidebarNavHtml]);
    }

    /**
     * 获取侧边栏当前菜单
     *
     * @param string $queryString URL请求串
     * @return bool|int
     * @throws Q_Exception
     */
    public static function getCurSidebarMenuId($queryString = '')
    {
        $queryString = empty($queryString) ? $_SERVER["QUERY_STRING"] : $queryString;

        $queryString = strtolower($queryString);

        $_cacheKey = $queryString;
        //做一下缓存，优化二次调用
        if (!empty(self::$_cache[__FUNCTION__][$_cacheKey])) {
            return self::$_cache[__FUNCTION__][$_cacheKey];
        }

        parse_str($queryString, $queryString);


        $curId  = 0;
        $likely = 0;

        //获取当前页面id值//用键值对获取
        $_param = array(
            'order' => 'sort ASC'
        );

        $menus = Q_DAL_Client::instance()->call('Admin_' . APP_NAME . 'Menu', 'Gets', $_param);

        foreach ($menus as $id => $_item) {
            if (empty($_item['query'])) {
                continue;
            }
            $query = strtolower($_item['query']);
            parse_str($query, $query);

            if ($query == $queryString) {
                $curId = $id;
                break;
            }

            $_sameNum = count(array_intersect_assoc($query, $queryString));
            if ($_sameNum > $likely) {
                $curId  = $id;
                $likely = $_sameNum;
            }

        }
        self::$_cache[__FUNCTION__][$_cacheKey] = $curId;
        return $curId;
    }

    /**
     * 获取导航栏菜单ID
     *
     * @param string $queryString
     * @return array
     * @throws Q_Exception
     */
    public static function getCurMenuId($queryString = '')
    {
        $curSidebarMenuId = self::getCurSidebarMenuId($queryString);

        $menus = Q_DAL_Client::instance()->call('Admin_' . APP_NAME . 'Menu', 'Gets');

        $curMenuIds = [$curSidebarMenuId];

        self::_getAllCurMenuIds($curSidebarMenuId, $menus, $curMenuIds);

        return $curMenuIds;
    }


    /**
     * 获取当前菜单ID
     *
     * @param $parentMenuId
     * @param $menus
     * @param $curSidebarMenuId
     * @return int
     */
    private static function _getAllCurMenuIds($parentMenuId, $menus, &$curSidebarMenuId)
    {
        $menuId = 0;
        foreach ($menus as $menu) {
            if ($menu['id'] == $parentMenuId) {
                $curSidebarMenuId[] = $menu['id'];
                if ($menu['pid'] != 0) {
                    $menuId = self::_getAllCurMenuIds($menu['pid'], $menus, $curSidebarMenuId);
                } else {
                    $menuId = $menu['id'];
                }
            }
        }

        return $menuId;
    }
}