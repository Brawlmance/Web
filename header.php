<?
ini_set('display_errors', 1);

include('keys.php');
$db = new mysqli($db_host, $db_user, $db_password, $db_name);


$day=floor(time()/60/60/24);
$lastpatch=$db->query("SELECT timestamp, id FROM patches WHERE timestamp<".time()."-60*60*24*2 ORDER BY timestamp DESC LIMIT 1")->fetch_array();
$lastpatchday=floor($lastpatch['timestamp']/60/60/24)+1; // patch stats start next day
$dayscondition="day>$day-$lastpatchday"; // http://api.steampowered.com/ISteamNews/GetNewsForApp/v0002/?appid=291550&count=30&maxlength=300&format=json

$rolenames=array("", "Tank", "Bruiser", "Glass cannon", "Balanced", "Ninja");

$v=10;
//$v=rand();

$totalgames=$db->query("SELECT SUM(games) FROM stats WHERE $dayscondition")->fetch_array()[0];
$totalwins=$db->query("SELECT SUM(wins) FROM stats WHERE $dayscondition")->fetch_array()[0];
$winratebalance=$totalgames/$totalwins/2;


/* comprobar la conexión */
if ($db->connect_errno) {
    printf("DB is down: %s\n", $db->connect_error);
    exit();
}

function legendName2divId($name) {
	return str_replace(" ", "", $name);
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Brawlmance - Brawlhalla high elo legend stats</title>
	<meta name="description" content="Brawlmance - Brawlhalla legend stats">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css/normalize.min.css?v=2">
	<link rel="stylesheet" href="/css/main.css?v=<?=$v?>">
</head>
<body>
  <div class="container" id="main">
	<header>
      <div id="menu">
		<ul>
			<li><a href="/">HOME</a></li>
			<li><a href="/about.php">ABOUT</a></li>
		</ul>
	  </div>
      <div id="socialmenu">
		<a href="https://twitter.com/intent/tweet?screen_name=Balbonator" class="twitter-mention-button" data-show-count="false"></a><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
	  </div>
      <div id="aggregationstatus">
		<?
		echo "Patch $lastpatch[id] | Games analyzed: ".number_format($totalgames);
		if($totalgames<100000) echo " <b>(WARNING: We are still aggregating patch data)</b>";
		?>
	  </div>
	</header>
	<div id="content">