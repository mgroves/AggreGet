<?php
	$daysback = 1.75;

	// this is where the parsing takes place
	// this script is called by a cron job every 10 minutes
	//
	// most feeds have the correct information in "link" and "title" tags
	//
	// reddit has the link as an anchor tag in the description
	// tailrank and instapaper too, but with slightly different formatting
	//
	// I also take out any leading "www" in the links, just to increase
	//	matches/show less duplicates
	//	currently I do not put "www" back into the links, but that could be
	//	done if some toolbox insists on "www" making his site not work
	//
	// Digg is done via API, not RSS, so it needs its own fancy parser which
	//	you'll find at the end of this script

	require("includes/dbfunc.php");						// PrepSQL / db connect
	require("includes/parsefunc.php");				// StringBetween
	
	// get the current #1 story
	$oldest = strtotime(date("Y-m-d H:i:s")) - ($daysback * 24 * 60 * 60);
	$sql = "SELECT news, url, count(*) AS cnt
					FROM aggro_news
					WHERE dtscrape >= " . PrepSQL(date("Y-m-d H:i:s",$oldest)) . "
					GROUP BY url
					HAVING count(*) > 1
					ORDER BY count(*) DESC, MAX(dtscrape) DESC
					LIMIT 1";
	$rs = mysql_fetch_assoc(mysql_query($sql));
	$varOldNo1 = $rs['url'];

	$aURL = array();
	$bigSQL = "INSERT IGNORE INTO aggro_news (title, url, site, dtscrape) VALUES ";
	$insideitem = false;
	$tag = ""; 
	$title = ""; 
	$description = ""; 
	$link = "";
	$site = "";
	$mixxlink = "";
	$aAttributes = array();

	function startElement($parser, $tagName, $attrs) { 
	   global $insideitem, $tag;
	   if ($insideitem) {
	   	$tag = $tagName;
	   	$aAttributes = $attrs;
	   } elseif ($tagName == "ITEM") { 
	   	$insideitem = true; 
	   } 
	}
	
	function characterData($parser, $data) { 
	   global $insideitem, $tag, $title, $description, $link, $mixxlink;
		  if ($insideitem) {
		       switch ($tag) { 
		           case "TITLE": 
		           $title .= $data; 
		           break; 
		           case "DESCRIPTION": 
		           $description .= $data; 
		           break; 
		           case "LINK": 
		           $link .= $data; 
		           break;
		           case "MIXX:SOURCE":
		           $mixxlink .= $data;
		           break;
		       }
		   } 
	}
	
	function endElement($parser, $tagName) { 
	   global $insideitem, $tag, $title, $description, $link, $mixxlink, $aURL, $site, $bigSQL; 

			// this works for delcious, instapaper, twitbuzz, and stumble
	   if ($tagName == "ITEM") { 

				//**************** reddit handling
	   		if($site == 4) {
					// reddit has to be contrarian!
					// also reddits that link to reddit aren't fully
					// qualified, but no one cares about them anyway, so skip them
	   		 $formURL = StringBetween("<br/> <a href=\"","\">[link]",$description);
	   		 if( (strpos($formURL,"http://") !== false) || (strpos($formURL,"https://") !== false) || (strpos($formURL,"ftp://") !== false) ) {
		       $formURL = str_replace("http://www.","http://",trim($formURL));
		       $aURL[] = $formURL;
			     $bigSQL .= "(" .
			     					 PrepSQL(trim($title)) . ", " .
			     					 PrepSQL(trim($formURL,"/")) . ", " .
			     					 PrepSQL($site) . ", " .
			     					 PrepSQL(date("Y-m-d H:i:s")) . "),";
		     }

				//****************** tailrank and instapaper handling
		    } else if($site == 5 || $site == 8) {
					// tailrank is a pain too
	   		 $formURL = StringBetween("a href=\"","\">",$description);
	   		 if( (strpos($formURL,"http://") !== false) || (strpos($formURL,"https://") !== false) || (strpos($formURL,"ftp://") !== false) ) {
		       $formURL = str_replace("http://www.","http://",trim($formURL));
		       $aURL[] = $formURL;
			     $bigSQL .= "(" .
			     					 PrepSQL(trim($title)) . ", " .
			     					 PrepSQL(trim($formURL,"/")) . ", " .
			     					 PrepSQL($site) . ", " .
			     					 PrepSQL(date("Y-m-d H:i:s")) . "),";
		      }

				//********************  mixx special tag
				} else if($site == 11) {
						$formURL = str_replace("http://www.","http://",trim($mixxlink));
		       	$aURL[] = $formURL;
			     	$bigSQL .= "(" .
			     					 PrepSQL(trim($title)) . ", " .
			     					 PrepSQL(trim($formURL,"/")) . ", " .
			     					 PrepSQL($site) . ", " .
			     					 PrepSQL(date("Y-m-d H:i:s")) . "),";

				//****************** all other handling				
	      } else {
	       $formURL = str_replace("http://www.","http://",trim($link));
	       $aURL[] = $formURL;
		     $bigSQL .= "(" .
		     					 PrepSQL(trim($title)) . ", " .
		     					 PrepSQL(trim($formURL,"/")) . ", " .
		     					 PrepSQL($site) . ", " .
		     					 PrepSQL(date("Y-m-d H:i:s")) . "),";
	      }

			 $title = ""; 
	     $description = ""; 
	     $link = "";
	     $mixxlink = "";
	     $insideitem = false; 
	   }
	}

	$sql = "SELECT site, rssurl, sitename
					FROM aggro_sites
					WHERE active <> '0'";
	$sqlquery = mysql_query($sql);

	while($rssFeed = mysql_fetch_assoc($sqlquery))
	{
		$site = $rssFeed['site'];
		
		// Create an XML parser 
		$xml_parser = xml_parser_create(); 
		
		// Set the functions to handle opening and closing tags 
		xml_set_element_handler($xml_parser, "startElement", "endElement"); 
		
		// Set the function to handle blocks of character data 
		xml_set_character_data_handler($xml_parser, "characterData"); 
		
		// Open the XML file for reading 
		// use curl instead of fopen
		$fp = curl_init();
		curl_setopt($fp, CURLOPT_URL, $rssFeed['rssurl']);
		curl_setopt($fp, CURLOPT_HEADER, 0);
		curl_setopt($fp, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($fp, CURLOPT_USERAGENT, "Opera/9.50 (Windows NT 5.1; U; en)");
		$data = curl_exec($fp);
		curl_close($fp);
		
		if($data != "") {
			if(!xml_parse($xml_parser, $data, true)) {
				// some of the RSS feeds aren't formed properly, so ignore any errors for now
			}
		}
		
		// Free up memory used by the XML parser 
		xml_parser_free($xml_parser);

		// let's insert once for each site, just for laughs
		mysql_query(RemoveTrail(",",$bigSQL));
	}
	
	// now do Digg, which is site 10
	$site = 10;
	$fp = curl_init();
	curl_setopt($fp, CURLOPT_URL, "http://services.digg.com/stories/popular?count=20&appkey=http%3A%2F%2Faggreget.com");
	curl_setopt($fp, CURLOPT_HEADER, 0);
	curl_setopt($fp, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($fp, CURLOPT_USERAGENT, "Opera/9.50 (Windows NT 5.1; U; en)");
	$data = curl_exec($fp);
	curl_close($fp);
	
	$aDiggs = explode("<story ",$data);

	// start at index 1, skip the header chaff
	for($i=1;$i<count($aDiggs);$i++)
	{
		$formURL = StringBetween("link=\"","\"",$aDiggs[$i]);
		$title = StringBetween("<title>","</title>",$aDiggs[$i]);
    $formURL = str_replace("http://www.","http://",trim($formURL));

		$bigSQL .= "(" .
			PrepSQL(trim($title)) . ", " .
			PrepSQL(trim($formURL,"/")) . ", " .
			PrepSQL($site) . ", " .
			PrepSQL(date("Y-m-d H:i:s")) . "),";
	}

	// remove all the trailing carriage returns from titles and such
	$bigSQL = str_replace("\n'","'",$bigSQL);

	// run the big SQL query already!
	mysql_query(RemoveTrail(",",$bigSQL));

  echo(mysql_error());

	// Parse FARK
	require("farkscraper.php");
	
	// see if there's a new #1 story
	$oldest = strtotime(date("Y-m-d H:i:s")) - ($daysback * 24 * 60 * 60);
	$sql = "SELECT news, title, url, count(*) AS cnt
					FROM aggro_news
					WHERE dtscrape >= " . PrepSQL(date("Y-m-d H:i:s",$oldest)) . "
					GROUP BY url
					HAVING count(*) > 1
					ORDER BY count(*) DESC, MAX(dtscrape) DESC
					LIMIT 1";
	$rs = mysql_fetch_assoc(mysql_query($sql));
	$varNewNo1 = $rs['url'];
	$varNewNo1ID = $rs['news'];

	if($varOldNo1 != $varNewNo1) {
		// let's tweet about the new #1!!
		// get the best title, also the one displayed on the page
		$sql = "SELECT n.title, s.sitename
				FROM aggro_news n
				INNER JOIN aggro_sites s ON n.site = s.site
				WHERE n.url = " . PrepSQL($varNewNo1) . "
				AND n.dtscrape >= " . PrepSQL(date("Y-m-d H:i:s",$oldest)) . "
				ORDER BY s.besttitle
				LIMIT 1";
		$rs = mysql_fetch_assoc(mysql_query($sql));
		Tweet("http://www.aggreget.com/aggrelink.php?ID=" . $varNewNo1ID . " " . str_replace("\"","'",$rs['title']));
		//Tweet("http://AggreGet.com " . str_replace("\"","'",$rs['title']));
	} else {
		// same old #1, no Tweet :(
	}

	exit();
?>