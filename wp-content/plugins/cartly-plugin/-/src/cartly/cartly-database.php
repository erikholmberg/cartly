<?php

include_once(sprintf('cartly-utilities.php', dirname(__FILE__)));

class Database
{
	// Public Methods
	public static function GetResults($sql)
	{
		global $wpdb;
		CartlyUtilities::IncludeWPConfig();
		return $wpdb->get_results($sql);
	}
	
	public static function GetResultsObject($sql)
	{
		global $wpdb;
		CartlyUtilities::IncludeWPConfig();
		return $wpdb->get_results($sql, OBJECT);
	}
	
	public static function Update($table, $data, $where)
	{
		global $wpdb;
		CartlyUtilities::IncludeWPConfig();
		return $wpdb->update($table, $data, $where);
	}
	
	public static function Delete($sql)
	{
		global $wpdb;
		CartlyUtilities::IncludeWPConfig();
		return $wpdb->query($sql);
	}
	
	public static function Insert($table, $data)
	{
		global $wpdb;
		CartlyUtilities::IncludeWPConfig();
		$wpdb->insert($table, $data);
		return $wpdb->insert_id;
	}
}

?>