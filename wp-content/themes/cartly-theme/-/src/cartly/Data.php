<?php

require_once('Utilities.php');

class Data
{
	// Public Methods
	public static function GetResults($sql)
	{
		global $wpdb;
		Utilities::IncludeWPConfig();
		return $wpdb->get_results($sql);
	}
	
	public static function GetResultsObject($sql)
	{
		global $wpdb;
		Utilities::IncludeWPConfig();
		return $wpdb->get_results($sql, OBJECT);
	}
}

?>