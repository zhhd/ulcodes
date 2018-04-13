<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/10
 * Time: 9:52
 */

define('BASE_DIR', dirname(dirname(__FILE__)));

require 'Core\SplClassLoader.php';
$splClassLoader = new SplClassLoader(BASE_DIR);
$splClassLoader->register();

\ulcodes\Core\Drive\Debug\Debug::enable();
\ulcodes\Core\Drive\Route\Route::resolve();