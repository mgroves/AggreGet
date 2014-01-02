<?php
	// hide teh errors!!!
	ini_set('display_errors','0');

	require("includes/dbfunc.php");						// PrepSQL / db connect

	// get the ID and validate it as a positive integer
	$varID = intval($_GET['ID']);
	if($varID <= 0) {
		header("Location: index.php");
		exit();
	}
	
	// look it up
	$sql = "SELECT n2.url, n2.title, n2.dtscrape, s.sitename
					FROM aggro_news n1
					INNER JOIN aggro_news n2 ON n1.url = n2.url
					INNER JOIN aggro_sites s ON n2.site = s.site
					WHERE n1.news = " . PrepSQL($varID) . "
					ORDER BY dtscrape DESC;";
	$historySqlquery = mysql_query($sql);
	if(mysql_num_rows($historySqlquery) <= 0) {
		header("Location: index.php");
		exit();
	}
	
	$pageTitle = "History : Aggreget";
	require("includes/header.php");
?>



<?
	while($historyRS = mysql_fetch_assoc($historySqlquery))
	{
		echo($historyRS['sitename'] . " => " . $historyRS['dtscrape'] . "<br />");
	}
?>

<?php
	require("includes/footer.php");
?>
