<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 27.12.14
 * Time: 15:52
 */

require_once 'conf.php';
require_once 'lib/time.php';
require_once 'lib/fs.php';

class Log
{
    // logging modes
    const DEBUG = 1;
    const WARNING = 2;
    const ERROR = 3;
    const CRITICAL = 4;
    const STAT = 10;
    const INFO = 11;

    protected $fileExtension = 'txt'; // log file extension
    protected $filePrefix = 'all'; // log file extension
    protected $file = null; // file name, without extension
    const MAX_FILE_SIZE = 1024000; //bytes
    protected $level = self::DEBUG;

    protected $dateFormat = null;

    protected function __construct($threshold=self::DEBUG, $filePrefix = 'all', $fileExtension = 'txt'){
        $this->level = $threshold;
        $this->filePrefix = $filePrefix;
        $this->fileExtension = $fileExtension;
        $this->dateFormat = PROJ_CONF::TIME_FORMAT;
    }

    public function setLevel($level)
    {
        if($level < self::DEBUG || $level > self::CRITICAL)
            return;
        $this->level = $level;

    }

    public function put($msg, $level = self::DEBUG, $context = null, $contextName = 'context')
    {
        if($this->level > $level)
            return true;
        $timestamp = Time::getLocal();
        if($context){
            $context = PHP_EOL.$contextName.':'.json_encode_u($context).PHP_EOL;
        }else{
            $context = '';
        }
        $str = self::levelToString($level).' ['.$timestamp->format(PROJ_CONF::TIME_FORMAT).'] '.$msg.$context.PHP_EOL;
        return $this->writeString($str);
    }

    public function debug($msg, $context = null)
    {
        return $this->put($msg, Log::DEBUG, $context);
    }

    public function warning($msg, $context = null)
    {
        return $this->put($msg, Log::WARNING, $context);
    }

    public function error($msg, $context = null)
    {
        return $this->put($msg, Log::ERROR, $context);
    }

    public function critical($msg, $context = null)
    {
        return $this->put($msg, Log::CRITICAL, $context);
    }

    public function stat($data, $description=''){
        return $this->put($description, Log::STAT, json_encode_u($data), 'json');
    }

    public function info($msg, $context = null)
    {
        return $this->put($msg, Log::INFO, $context);
    }

    protected static function levelToString($lvl)
    {
        switch($lvl){
            case self::DEBUG : return 'DEBUG';
            case self::WARNING : return 'WARNING';
            case self::ERROR : return 'ERROR';
            case self::CRITICAL : return 'CRITICAL';

            case self::STAT : return 'STAT';
            case self::INFO : return 'INFO';
        }
        return 'UNDEFINED';
    }

    protected function getFilePattern(){
        return '/'.$this->filePrefix.' (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\.'.$this->fileExtension.'$/';
    }

    protected function getFileDateFormat(){
        return $this->dateFormat;
    }

    protected function getDefaultPermission(){
        return 0777;
    }

    protected function createNewFile()
    {
        $fileDateTime = Time::getLocal();
        $logHead = '# Log file, created at ['.$fileDateTime->format($this->dateFormat).']'.PHP_EOL;
        $file = $this->file;
        $this->setFileName($this->filePrefix, $fileDateTime, $this->fileExtension);

        if(!file_put_contents($this->getFileName(), $logHead)){
            error_log('Can\'t write to file '.$this->getFileName());
            $this->file = $file;
            return false;
        }
        return true;
    }

    protected function prepareFile()
    {
        if($this->file){
            if(filesize($this->file) > self::MAX_FILE_SIZE) // check log file max size
                return $this->createNewFile();
        }

        $logDir = basePath(LOG_CONF::LOGS_PATH);
        //bool mkdir ( string $pathname [, int $mode = 0777 [, bool $recursive = false [, resource $context ]]] )
        if(!file_exists($logDir)){
            if(!mkdir ($logDir, 0777)) {
                error_log('Can\'t create dir ' . $logDir);
                return false;
            }
        }
        $handle = opendir($logDir);
        if (!$handle) {
            error_log('Can\'t open dir '.$logDir);
            return false;
        }

        $pat = $this->getFilePattern();
        $fileDateTime = null;
        while (false !== ($entry = readdir($handle))) {
            if($entry == "." || $entry == "..")
                continue;
            if(filesize(makePath($logDir,$entry)) > self::MAX_FILE_SIZE) // check log file max size
                continue;
            if(!preg_match($pat,$entry,$matches)) // check log file by name, trying to get datetime
                continue;

            $tmpTime = Time::getLocalFromString($matches[1], $this->dateFormat); // getting time from filename

            if($fileDateTime === null){ // if first, initialize $fileDateTime
                $fileDateTime = $tmpTime;
                continue;
            }

            if(date_diff($fileDateTime, $tmpTime)->invert == 0) { // if found latest, set fileDateTime to it
                $fileDateTime = $tmpTime;
            }
        }
        closedir($handle);

        if($fileDateTime === null) { // if found nothing, use current time
            return $this->createNewFile();
        }

        $this->setFileName($this->filePrefix, $fileDateTime, $this->fileExtension);
        return true;
    }

    protected function writeString($string)
    {
        if(!$this->prepareFile()){
            error_log('Can\'t prepare to write to log '.$this->getFileName());
            return false;
        }
        return file_put_contents($this->getFileName(), $string, FILE_APPEND);
    }

    protected function truncate()
    {
        if(!$this->prepareFile()){
            error_log('Can\'t prepare to truncate to log '.$this->getFileName());
        }
        return file_put_contents($this->getFileName(), '');
    }

    protected function setFileName($prefix, $dateTime, $extension)
    {
        $logFile = $prefix.' '.$dateTime->format(PROJ_CONF::TIME_FORMAT).'.'.$extension;
        $this->file = basePath(LOG_CONF::LOGS_PATH, $logFile);
    }

    protected function getFileName()
    {
        return $this->file;
    }

    public static function get($statName = 'all', $logLevel = self::DEBUG, $fileExtension = 'txt'){
        if(self::$logs === null){
            self::$logs = array();
        }
        foreach(self::$logs as &$l){
            if($l->filePrefix == $statName)
                return $l;
        }
        $log = new self($logLevel, $statName, $fileExtension);
        self::$logs += array($log);
        return $log;

    }

    private static $logs = null;
}