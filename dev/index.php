<?php
	$daysback = 1.75;

	// hide teh errors!!!
	ini_set('display_errors','0');

	require("includes/dbfunc.php");						// PrepSQL / db connect
	
	$pageTitle = "Aggreget";
	require("includes/header.php");
?>

	<p>These links are so popular, they appear on multiple news sites.</p>

<?php
	// get latest scrape
	$sql = "select max(dtscrape) AS maxDT
					from aggro_news";
	$sqlquery = mysql_query($sql);
	$rs = mysql_fetch_assoc($sqlquery);
	$varLatest = $rs['maxDT'];


	$classNum = 1;
	$oldest = strtotime(date("Y-m-d H:i:s")) - ($daysback * 24 * 60 * 60);
	$sql = "SELECT news, url, count(*) AS cnt
					FROM aggro_news
					WHERE dtscrape >= " . PrepSQL(date("Y-m-d H:i:s",$oldest)) . "
					GROUP BY url
					HAVING count(*) > 1
					ORDER BY count(*) DESC, MAX(dtscrape) DESC
					LIMIT 10";
	$sqlquery = mysql_query($sql);
	if(mysql_num_rows($sqlquery) > 0) {
		echo("<ul>\n");
		while($rs = mysql_fetch_assoc($sqlquery))
		{
			if($classNum <= 6) {
				echo("<li class=\"s-" . $classNum . "\">\n");
			} else {
				echo("<li>\n");
			}
			echo("	<div class=\"number\">" . $classNum . "</div>\n");

			$classNum++;

			// use the ID to look up the title as well as the sites it's been indexed on
			// only show the sites from the window!
			$sql = "SELECT n.title, s.sitename
							FROM aggro_news n
							INNER JOIN aggro_sites s ON n.site = s.site
							WHERE n.url = " . PrepSQL($rs['url']) . "
							AND n.dtscrape >= " . PrepSQL(date("Y-m-d H:i:s",$oldest)) . "
							ORDER BY s.besttitle";
			$sitesqlquery = mysql_query($sql);
			$siteRS = mysql_fetch_assoc($sitesqlquery);
			$varTitle = $siteRS['title'];
			$sitelist = $siteRS['sitename'];
			while($siteRS = mysql_fetch_assoc($sitesqlquery))
			{
				$sitelist .= ", " . $siteRS['sitename'];
			}
			//echo("<div class=\"link\"><a href=\"" . $rs['url'] . "\">" . $varTitle . "</a></div>\n");
			echo("<div class=\"link\"><a href=\"aggrelink.php?ID=" . $rs['news'] . "\">" . $varTitle . "</a></div>\n");
			echo("<div class=\"sites\">[" . $sitelist . "]</div>\n");
			echo("</li>\n");
		}
		echo("</ul>\n");
	} else {
		echo("<p>Nothing.</p>\n");
	}

	require("includes/footer.php");
?>
