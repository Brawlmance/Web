<?
ini_set('display_errors', 1);

include('keys.php');
$db = new mysqli($db_host, $db_user, $db_password, $db_name);


$day=floor(time()/60/60/24);
$lastpatch=$db->query("SELECT timestamp, id FROM patches WHERE timestamp<".time()."-60*60*24*2 ORDER BY timestamp DESC LIMIT 1")->fetch_array();
$lastpatchday=floor($lastpatch['timestamp']/60/60/24)+1; // patch stats start next day
$dayscondition="day>$day-$lastpatchday"; // http://api.steampowered.com/ISteamNews/GetNewsForApp/v0002/?appid=291550&count=30&maxlength=300&format=json

$rolenames=array("", "Tank", "Warrior", "Hunter", "Hybrid", "Assasin");

$v=17;
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
function weaponId2Name($name) {
	switch($name) {
		case 'RocketLance': return "Rocket Lance"; break;
		case 'Pistol': return "Blasters"; break;
		case 'Fists': return "Gauntlets"; break;
		case 'Katar': return "Katars"; break;
		default: return $name;
	}
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Brawlmance - Brawlhalla Legend winrate and stats</title>
	<meta name="description" content="Brawlmance - Brawlhalla legend stats">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">
	
	<link rel="stylesheet" href="/css/normalize.min.css?v=2">
	<link rel="stylesheet" href="/css/main.css?v=<?=$v?>">
	<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
</head>
<body>
  <div class="container" id="main">
	<header>
      <div id="menu">
		<ul>
			<li><a href="/"><img src="/img/logo.png" alt="Brawlmance" title="Brawlmance" /> HOME</a></li>
			<li><a href="/weapons.php">WEAPONS</a></li>
			<li><a href="/about.php">ABOUT</a></li>
		</ul>
	  </div>
      <div id="socialmenu">
		<a href="https://twitter.com/intent/tweet?screen_name=Balbonator" class="twitter-mention-button" data-show-count="false"></a><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
	  </div>
      <div id="aggregationstatus">
		<?
		echo "Patch $lastpatch[id] | Games analyzed: ".number_format($totalgames);
		if($totalgames<300000) echo " <b>(WARNING: We are still aggregating patch data)</b>";
		?>
	  </div>
	</header>
	<div id="content">