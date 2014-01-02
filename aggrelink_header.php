<?

	$newsID = intval($_GET["news"]);
	if($newsID <= 0) {
		header("Location: index.php");
		exit();
	}

	require("includes/dbfunc.php");						// PrepSQL / db connect

	$sql = "SELECT url
					FROM aggro_news
					WHERE news = " . PrepSQL($_GET["news"]);
	$sqlquery = mysql_query($sql);
	if($rs = mysql_fetch_assoc($sqlquery)) {
		$varUrl = $rs["url"];
	} else {
		header("Location: index.php");
		exit();
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?=$pageTitle;?></title>
	<link rel="shortcut icon" href="images/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="css/style_screen.css" media="screen" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<base target=_top>
</head>
<body>
	<div id="top">
		<h1><a href="/"><span>Aggreget</span></a></h1>
	</div>

	<div style="float: left; width: 15%;">
		<p><a href="http://www.aggreget.com">[Back to Aggreget]</a></p>
	</div>
	<div style="width: 70%; float: left; font-size: 11px;">
		<p>Make <a href="http://www.aggreget.com">Aggreget</a> your homepage</a>,  or <a href="http://twitter.com/aggreget">follow Aggreget on Twitter</a> and always stay up to date!</p>
	</div>
	
	<div style="float: right; width: 15%;">
		<p><a href="<?=$varUrl;?>">[Remove frame]</a></p>
	</div>

</body>
</html>