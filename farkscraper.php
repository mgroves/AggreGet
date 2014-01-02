<?
	require_once("includes/dbfunc.php");						// PrepSQL / db connect
	require_once("includes/parsefunc.php");				// StringBetween

	// now do Fark, which is site 12
	$site = 12;
	$fp = curl_init();
	curl_setopt($fp, CURLOPT_URL, "http://www.fark.com");
	curl_setopt($fp, CURLOPT_HEADER, 0);
	curl_setopt($fp, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($fp, CURLOPT_USERAGENT, "Opera/9.50 (Windows NT 5.1; U; en)");
	$data = curl_exec($fp);
	curl_close($fp);

	// <div class="mainDate">Thursday, November 06, 2008</div>
	$fark_maindate = date("l, F d, Y",strtotime("-1 day",time()));
	$yesterdata = explode("<div class=\"mainDate\">" . $fark_maindate . "</div>", $data);
	$aHeadlines = explode("<tr class=\"headlineRow\">",$yesterdata[1]);

	$farkSQL = "INSERT IGNORE INTO aggro_news (title, url, site, dtscrape) VALUES ";
	for($i=1;$i<count($aHeadlines);$i++)
	{	
		//echo("<xmp>" . $aHeadlines[1] . "</xmp>");
		$url = StringBetween("l=","\"",$aHeadlines[$i]);
		$title = StringBetween("<span class=\"headline\">","</span>",$aHeadlines[$i]);

		// get number of comments
		$aSubHeadline = explode("comments.pl",$aHeadlines[$i]);
		$numcomments = StringBetween("\">(",")",$aSubHeadline[1]);

		// insert into the DB if the number of comments are past the threshold
		if(($numcomments == "lots") || (intval($numcomments) > 100)) {
			$farkSQL .= "(" .
							 PrepSQL(trim($title)) . ", " .
							 PrepSQL(trim(urldecode($url),"/")) . ", " .
							 PrepSQL($site) . ", " .
							 PrepSQL(date("Y-m-d H:i:s")) . "),";
		}
	}


	// remove all the trailing carriage returns from titles and such
	$farkSQL = str_replace("\n'","'",$farkSQL);

	// run the big SQL query already!
	$farkSQL = RemoveTrail(",",$farkSQL);
	
	mysql_query($farkSQL);
?>