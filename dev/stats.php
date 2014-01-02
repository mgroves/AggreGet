<?php
	// hide teh errors!!!
	ini_set('display_errors','0');

	require("includes/dbfunc.php");						// PrepSQL / db connect

	$pageTitle = "Stats : Aggreget";
	require("includes/header.php");
?>

<h2>Links Aggregot This Week</h2>

<?
	$sql = "SELECT MONTH(dtscrape) AS mnth, DAY(dtscrape) AS dy, YEAR(dtscrape) AS yr, COUNT(*) AS tot
					FROM aggro_news
					WHERE dtscrape >= " . PrepSQL(date("Y-m-d 00:00:01",strtotime("-7 days"))) . "
					GROUP BY MONTH(dtscrape), DAY(dtscrape), YEAR(dtscrape)
					ORDER BY YEAR(dtscrape), MONTH(dtscrape), DAY(dtscrape)";
	$sqlquery = mysql_query($sql);
	echo("<ul>\n");
	while($rs = mysql_fetch_assoc($sqlquery))
	{
		$str_fulldate = $rs['mnth'] . "/" . $rs['dy'] . "/" . $rs['yr'];
		$str_dayname = date("D",strtotime($str_fulldate));
		echo("<li>" . $str_fulldate . " (" . $str_dayname . "): " . $rs['tot'] . "</li>\n");
	}
	echo("</ul>\n");
?>

<h2>Most Popular Sites</h2>

<?
	$sql = "SELECT SUBSTR((REPLACE(url,'http://','')),1,(INSTR(REPLACE(url,'http://',''),'/'))-1) AS domain, COUNT(*)
					FROM aggro_news
					WHERE SUBSTR((REPLACE(url,'http://','')),1,(INSTR(REPLACE(url,'http://',''),'/'))-1) <> ''
					GROUP BY SUBSTR((REPLACE(url,'http://','')),1,(INSTR(REPLACE(url,'http://',''),'/'))-1)
					ORDER BY COUNT(*) DESC
					LIMIT 20";
	$sqlquery = mysql_query($sql);
	echo("<ol>\n");
	while($rs = mysql_fetch_assoc($sqlquery))
	{
		echo("<li><a href=\"http://" . $rs['domain'] . "\">" . $rs['domain'] . "</a></li>\n");
	}
	echo("</ol>\n");
?>

<?php
	require("includes/footer.php");
?>
