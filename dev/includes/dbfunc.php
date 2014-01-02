<?php
	// connect to MySQL database
	$db = mysql_connect("mkgdb01.matthewkgroves.com","svc_aggro","crag18crag");
	if(!$db) {
		die("Error connecting to MySQL database.");
	}
	mysql_select_db("aggroget" ,$db);
	
	// use stripslashes and mysql_real_escape_string PHP functions
	// to sanitize a string for use in an SQL query
	//
	// also puts single quotes around the string
	//
	function PrepSQL($value)
	{
  	// Stripslashes
   	if(get_magic_quotes_gpc()) {
    	$value = stripslashes($value);
   	}

   	// Quote
   	$value = "'" . mysql_real_escape_string($value) . "'";

   	return($value);
	}
?>