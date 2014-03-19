<?php

class Utilities
{
	// Public Methods
	public static function GetDB()
	{
		static::IncludeWPConfig();
	}
	
	public static function PrintMoney($amount)
	{
		return '$'.number_format(floatval($amount), 2);
	}
	
	public static function GetOrderNumber($orderId)
	{
		$orderNumber = $orderId < 10 ? '0'.$orderId : $orderId;
		return 100000 + $orderNumber;
	}
	
	public static function GetOrderId($orderNumber)
	{
		return $orderNumber - 100000;
	}
	
	public static function SendJSON($status, $message, $code = '000', $extra = '')
	{
		echo json_encode(array('status' => $status, 'message' => $message, 'code' => $code, 'extra' => $extra));
	}
	
	public static function LogError($message)
	{
		error_log("\r\n".date('Y-m-d h:m:s')."\t".$message, 3, getcwd().'/-/errors/errors.log');
	}
	
	public static function IncludeWPConfig()
	{
		if (!isset($table_prefix))
		{
			global $confroot;
			static::FindWPConfig(dirname(dirname(__FILE__)));
			include_once($confroot.'/wp-config.php');
		}
	}
	
	public static function RandomString()
	{
    	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    	$string = '';
    	
    	for ($i = 0; $i < 10; $i++)
    	{
    		$string .= $chars[rand(0, strlen($chars)-1)];
    	}
    	
    	return $string;
    }
	
	// Private Methods
	private static function FindWPConfig($dirrectory)
	{
		global $confroot;
	
		foreach(glob($dirrectory."/*") as $f)
		{
			if (basename($f) == 'wp-config.php' )
			{
				$confroot = str_replace("\\", "/", dirname($f));
				return true;
			}
	
			if (is_dir($f))
			{
				$newdir = dirname(dirname($f));
			}
		}
	
		if (isset($newdir) && $newdir != $dirrectory)
		{
			if (static::FindWPConfig($newdir))
			{
				return false;
			}	
		}
	
		return false;
	}
}

?>