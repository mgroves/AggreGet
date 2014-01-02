<?php
	// parsing helper functions
	// these might be better written in regex?
	// who knows, I suck at.

	// returns the first instance of a string between two strings
	//
	// example:
	//	$res = StringBetween("<em>","</em>","<em>foo bar</em>");
	//	now $res == "foo bar"
	//
	function StringBetween($strStart,$strEnd,$strSubject)
	{
		$aTemp = explode($strStart,$strSubject);
		$aTemp = explode($strEnd,$aTemp[1]);
		return($aTemp[0]);
	}
	
	// removes a given substring from the end of a string
	//
	// example:
	//	$res = RemoveTrail(" ,","this, this, and this,");
	//	now $res == "this, this, and this"
	//
	function RemoveTrail($trail,$subject)
	{
		// this string just needs to be any ridiculous string that probably
		// will never occur outside of some contrived condition
		$ridiculous = "@#@#@#@#@#@#@#@";

		return(str_replace($trail . $ridiculous,"",$subject . $ridiculous));
	}
	
	// sends a message to the AggreGet Twitter account
	function Tweet($message)
	{
		if(strlen($message) > 140) {
			$message = substr($message, 0, 137) . "...";
		}

		// Set username and password
		$username = "AggreGet";
		$password = "thunder";
	
		// The twitter API address
		$url = "http://twitter.com/statuses/update.xml";
	
		// Alternative JSON version
		// $url = 'http://twitter.com/statuses/update.json';
	
		// Set up and execute the curl process
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_POST, 1);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "status=" . $message);
		curl_setopt($curl_handle, CURLOPT_USERPWD, $username . ":" . $password);
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
	}		
?>