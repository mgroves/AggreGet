<?

	$newsID = intval($_GET["ID"]);
	if($newsID <= 0) {
		header("Location: index.php");
		exit();
	}

	require("includes/dbfunc.php");						// PrepSQL / db connect

	$sql = "SELECT news, url, title
					FROM aggro_news
					WHERE news = " . PrepSQL($_GET["ID"]);
	$sqlquery = mysql_query($sql);
	if($rs = mysql_fetch_assoc($sqlquery)) {
		$varUrl = $rs["url"];
		$varTitle = $rs["title"];
		$varID = $rs["news"];
	} else {
		header("Location: index.php");
		exit();
	}
?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<title><?=$varTitle?> : AggreGet</title>
	</head>
	
<frameset rows="137,*">
	<frame src="aggrelink_header.php?news=<?=$varID?>" scrolling=no marginwidth=0 marginheight=0>
	<frame src="<?=$varUrl?>">
	
	<noframes>
		Your browser does not support frames. AggreGet uses frames to display
		featured news stories.  You can view 
		the news story at <a href="<?=$varUrl?>">this link</a>.
	</noframes>
</frameset>

</html>