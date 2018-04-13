<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/11
 * Time: 11:43
 */

namespace ulcodes\Extend\Controller;


use ulcodes\Core\Drive\Globals\Globals;
use ulcodes\Core\Drive\Helper\Helper;

class Controller
{
    private $_ext    = ['.php', '.html'];
    private $_assign = [];

    /**
     * 渲染模板
     *
     * @param null  $view
     * @param array $assign
     */
    public function display($view = null, $assign = [])
    {
        $this->initVar();
        $module   = ucfirst(Globals::get('module'));
        $base_dir = BASE_DIR;
        $sep      = DIRECTORY_SEPARATOR;
        if ($view == null) {
            $controller = ucfirst(Globals::get('controller'));
            $action     = Globals::get('action');
            $viewPath   = "{$base_dir}{$sep}app{$sep}module{$sep}{$module}Bundle{$sep}View{$sep}$controller{$sep}{$action}";
        } else {
            $viewPath = "{$base_dir}{$sep}app{$sep}module{$sep}{$module}Bundle{$sep}View{$sep}{$view}";
        }
        $viewPath = str_replace('\\', $sep, $viewPath);
        $assign   = array_merge($assign, $this->_assign);
        $this->initVar();
        extract($assign);

        foreach ($this->_ext as $item) {
            if (file_exists(sprintf("%s.%s", $viewPath, $item))) {
                include $viewPath;
                break;
            }
        }
    }


    /**
     * 渲染变量
     *
     * @param $key
     * @param $value
     */
    public function assign($key, $value)
    {
        $this->_assign[$key] = $value;
    }

    public function initVar()
    {
        $objectUrl = Helper::getCurrentObjectUrl();

        $this->assign("__css__", "{$objectUrl}/resource/css");
        $this->assign("__js__", "{$objectUrl}/resource/js");
        $this->assign("__img__", "{$objectUrl}/resource/img");
    }
}