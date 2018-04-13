<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/10
 * Time: 10:56
 */

namespace ulcodes\Core\Drive\Debug;


use ulcodes\Core\Drive\Helper\Helper;
use ulcodes\Core\Drive\Log\Log;

class ErrorHandle
{
    private $thrownErrors = 0x1FFF; // E_ALL - E_DEPRECATED - E_USER_DEPRECATED
    private $loggedErrors = 0;

    /**
     * 注册异常处理函数
     *
     * @return static
     */
    public static function register()
    {
        register_shutdown_function(__CLASS__ . '::handleFatalError');
        $handler = new static();
        if (null === $prev = set_error_handler(array($handler, 'handleError'))) {
            restore_error_handler();
            set_error_handler(array($handler, 'handleError'), $handler->thrownErrors | $handler->loggedErrors);
        }
        restore_exception_handler();
        set_exception_handler(array($handler, 'handleException'));
        return $handler;
    }

    /**
     * 致命错误
     *
     * @param array|null $error
     */
    public static function handleFatalError(array $error = null)
    {
        if (null === $error) {
            $error = error_get_last();
        }

        if ($error && $error['type'] &= E_PARSE | E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR) {
            self::__echo($error);
            Log::error(Helper::json($error));
        }
    }

    /**
     * @param $type
     * @param $message
     * @param $file
     * @param $line
     */
    public function handleError($type, $message, $file, $line)
    {
        $error = [
            'type'    => $type,
            'message' => $message,
            'file'    => $file,
            'line'    => $line,
        ];
        self::__echo($error);
        Log::info(Helper::json($error));
    }

    /**
     * @param \Exception $exception
     * @param array|null $error
     */
    public function handleException($exception, array $error = null)
    {
        $error['message'] = $exception->getMessage();
        if (strrpos($error['message'], 'SQLSTATE') !== false) {
            $last_sql         = Helper::last_mysql()->lastSql();
            $error['message'] .= $last_sql;
        }
        self::__echo($error);
        Log::info(Helper::json($error));
    }

    public static function __echo($error)
    {
        if (DEBUG)
            echo Helper::json($error);
    }
}