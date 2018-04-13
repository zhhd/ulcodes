<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/10
 * Time: 15:07
 */

namespace ulcodes\Core\Drive\Route;


use ulcodes\Core\Drive\Config\Config;
use ulcodes\Core\Drive\Doc\DocParse;
use ulcodes\Core\Drive\Doc\DocVerify;
use ulcodes\Core\Drive\Globals\Globals;
use ulcodes\Core\Drive\Helper\Helper;

class NormalRoute implements RouteInterface
{
    /**
     * 模块
     *
     * @var
     */
    private $_module;
    /**
     * 控制器
     *
     * @var
     */
    private $_controller;
    /**
     * 函数
     *
     * @var
     */
    private $_action;

    /**
     * 控制器类名
     *
     * @var
     */
    private $_controller_class_name;
    /**
     * 去除get参数之后的地址
     *
     * @var array
     */
    private $_path;
    /**
     * 解析出的get参数
     *
     * @var
     */
    private $_get;

    private $_filter = ['app', 'log', 'resource', 'ulcodes'];

    public function getControllerClassName()
    {
        return $this->_controller_class_name;
    }

    /**
     * 将url解析成地址和参数
     *
     */
    private function parse_url()
    {
        unset($_GET['s']);
        $request_uri = $_SERVER['REQUEST_URI'];
        $parse_url   = parse_url($request_uri);
        $get         = [];
        if (isset($parse_url['query'])) {
            $querys = preg_split('/&/i', $parse_url['query']);
            foreach ($querys as $query) {
                if (!empty(trim($query))) {
                    $query = preg_split('/=/i', $query);
                    if (!isset($query[0])) continue;
                    $get[$query[0]] = urldecode(isset($query[1]) ? $query[1] : '');
                }
            }
        }
        $this->_path = array_filter(preg_split('/\\//i', $parse_url['path']));
        $this->_get  = $get;
    }

    /**
     * 解析路由
     * @return RouteInterface
     * @throws \Exception
     */
    public function resolve()
    {
        $this->parse_url();
        $dir_level = Config::get('dir_level');
        for ($i = 1; $i < $dir_level; $i++)
            array_shift($this->_path);

        $this->_module     = ucfirst(array_shift($this->_path));
        $this->_controller = ucfirst(array_shift($this->_path));
        $this->_action     = str_replace('.html', '', array_shift($this->_path));


        if (in_array(strtolower($this->_module), $this->_filter)) {
            throw new \Exception('未找到对应模块');
        }

        $class_name = "app\\module\\{$this->_module}Bundle\\Controller\\{$this->_controller}Controller";
        if (class_exists($class_name)) {
            $this->_controller_class_name = $class_name;
        }
        return $this;
    }

    /**
     * 运行
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function run()
    {
        $action = $this->_action . 'Action';

        if (empty($this->_controller_class_name)) {
            throw new \Exception('未找到对应模块');
        }

        $class = new $this->_controller_class_name();
        if (!method_exists($class, $action)) {
            throw new \Exception("未找到对应{$action}");
        }

        ob_start();

        Globals::set('module', $this->_module);
        Globals::set('controller', $this->_controller);
        Globals::set('action', $this->_action);
        Globals::set('get', array_merge($this->_get, $_GET));
        Globals::set('post', $_POST);
        Globals::set('input', array_merge(Globals::get('get'), Globals::get('post')));

        $reflection = new \ReflectionClass($this->_controller_class_name);
        $method     = $reflection->getMethod($action);
        $params     = $method->getParameters();
        $verify     = true;
        if (!empty($params)) {
            $docComment   = $method->getDocComment();
            $docParse     = new DocParse($docComment);
            $parserValues = $docParse->parse();

            $docVerify = new DocVerify();
            $verify    = $docVerify->verify($parserValues);
        }

        $parameter = [];
        foreach ($params as $item) {
            $value = Helper::input($item->name);
            if (!is_null($value)) {
                $parameter[$item->name] = $value;
            } elseif ($item->isDefaultValueAvailable()) {
                $parameter[$item->name] = $item->getDefaultValue();
            } else {
                $parameter[$item->name] = null;
            }
        }
        if ($verify) {
            $result = call_user_func_array([$class, $action], $parameter);
            if (is_array($result)) {
                echo Helper::json($result);
            } elseif (is_object($result)) {
            } else {
                echo $result;
            }
        }
        ob_end_flush();
    }
}