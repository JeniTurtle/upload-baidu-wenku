<?php
/**
* @file Log.php
* @author Tomorrow
* @description 日志执行文件
*  
**/

define("PATH", dirname(__FILE__) . '/../log/upload.log');

class Plat_Log {
	private static $_odp;
	private static $_bingoLog;
	private static $_logPath = PATH;

	public static function isODP()
	{
		return self::$_odp;
	}

	public static function hasLog()
	{
		if (!is_object(self::$_bingoLog)) {
			self::$_bingoLog = new Log(self::$_logPath);
		}
	}

	public static function setOdp($is_odp = false)
	{
		self::$_odp = $is_odp;
	}

	public static function setLogPath($path)
	{
		self::$_logPath = $path;
    }  

	public static function debug($str, $errno = 0, $arrArgs = null, $depth = 0)
	{
		if (self::isODP()) {
			// Bd_Log::debug($str, $errno = 0, $arrArgs = null, $depth+1);
		}
		else {
			self::hasLog();
			self::getFileLine($file, $line);
			self::$_bingoLog->debug($str, $file, $line);

		}
	}

	public static function notice($str, $errno = 0, $arrArgs = null, $depth = 0)
    {
        if (self::isODP()) {
            // Bd_Log::notice($str, $errno = 0, $arrArgs = null, $depth+1);
        }
        else {
			self::hasLog();
			self::getFileLine($file, $line);
            self::$_bingoLog->notice($str, $file, $line);
        }
    }

	public static function warning($str, $errno = 0, $arrArgs = null, $depth = 0)
    {
        if (self::isODP()) {
            // Bd_Log::warning($str, $errno = 0, $arrArgs = null, $depth+1);
        }
        else {
			self::hasLog();
			self::getFileLine($file, $line);
            self::$_bingoLog->warning($str, $file, $line, $errno);

        }
    }

	public static function fatal($str, $errno = 0, $arrArgs = null, $depth = 0)
    {
        if (self::isODP()) {
            // Bd_Log::fatal($str, $errno = 0, $arrArgs = null, $depth+1);
        }
        else {
			self::hasLog();
			self::getFileLine($file, $line);
            self::$_bingoLog->error($str, $file, $line, $errno);
        }
    }

	public static function addNotice($key, $value)
	{
		if (self::isODP()) {
			// Bd_Log::addNotice($key, $value);
		}
		else {
			self::hasLog();
			self::getFileLine($file, $line);
			self::$_bingoLog->notice("'$key':'$value'", $file, $line);
		}
	
	}

	public static function getFileLine(&$file, &$line)
	{
        $trace = debug_backtrace();
		//var_dump($trace);exit;
		$depth = 1;
		
        $file = isset( $trace[$depth]['file'] )
                              ? $trace[$depth]['file'] : "" ;
        $line = isset( $trace[$depth]['line'] )
                              ? $trace[$depth]['line'] : "";
	}
}

