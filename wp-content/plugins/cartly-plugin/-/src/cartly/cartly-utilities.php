<?php

class CartlyUtilities
{
	// Public Methods
	public static function PrintMoney($amount)
	{
		return '$'.number_format(floatval($amount), 2);
	}
	
	public static function GetOrderNumber($orderId)
	{
		$orderNumber = $orderId < 10 ? '0'.$orderId : $orderId;
		return 100000 + $orderNumber;
	}
	
	public static function GetURL()
	{
		$url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
		$url .= ( $_SERVER["SERVER_PORT"] !== 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
		$url .= $_SERVER["REQUEST_URI"];
		return $url;
	}
	
	public static function GetBaseURL()
	{
		$url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
		$url .= ( $_SERVER["SERVER_PORT"] !== 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
		
		$pieces = explode('&', $_SERVER["REQUEST_URI"]);
		
		$url .= $pieces[0];
		
		return $url;
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