<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 06.12.14
 * Time: 15:44
 */

require_once 'conf.php';

class Log
{
    private static $LEVELS = array('DEBUG', 'WARNING', 'ERROR', 'CRITICAL');
    private static $LOG_LEVEL = null;
    private $logFile = null;

    const DEBUG = 0;
    const WARNING = 1;
    const ERROR = 2;
    const CRITICAL = 3;

    function init($logFile = 'all.log')
    {
        if (self::$LOG_LEVEL === null) ;
        self::$LOG_LEVEL = array_search(LOG_CONF::MODE, self::$LEVELS);
    }

    private function canPutToLog($lvl)
    {
        return $lvl >= self::$LOG_LEVEL;
    }

    function put($msg, $lvl = self::DEBUG)
    {
        if (!self::canPutToLog($lvl)) return true;
        $toPut = self::LEVELS . ' [' . date('Y-m-d H:i:s') . ']' . $msg . PHP_EOL;
        $ok = file_put_contents(self::logFile, $toPut, FILE_APPEND);
        return $ok;
    }

    function warning($msg)
    {
        return self::put($msg, self::WARNING);
    }

    function error($msg)
    {
        return self::put($msg, self::ERROR);
    }

    function critical($msg)
    {
        return self::put($msg, self::CRITICAL);
    }
}

$log = new Log();
$log->init();

