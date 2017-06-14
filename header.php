<?php
ini_set('display_errors', 1);

include('keys.php');
$db = new mysqli($db_host, $db_user, $db_password, $db_name);
if ($db->connect_errno) {
    printf("DB is down: %s\n", $db->connect_error);
    exit();
}
$db->set_charset("utf8mb4");

$day=floor(time()/60/60/24);
$lastpatch=$db->query("SELECT timestamp, id FROM patches WHERE changes='1' AND timestamp<".time()."-60*60*24*2 ORDER BY timestamp DESC LIMIT 1")->fetch_array();
if(isset($_GET['patch']) && $_GET['patch']!=$lastpatch['id']) {
	$lastpatch=$db->query("SELECT timestamp, id FROM patches WHERE changes='1' AND id='".$db->real_escape_string($_GET['patch'])."' ORDER BY timestamp DESC LIMIT 1");
	if($lastpatch->num_rows==0) die('Patch not found'); else $lastpatch=$lastpatch->fetch_array();
	$nextpatch=$db->query("SELECT timestamp, id FROM patches WHERE changes='1' AND timestamp>$lastpatch[timestamp] ORDER BY timestamp LIMIT 1");
	if($nextpatch->num_rows==0) die('Next patch not found'); else $nextpatch=$nextpatch->fetch_array();
	$lastpatchday=floor($lastpatch['timestamp']/60/60/24)+1; // patch stats start next day
	$nextpatchday=floor($nextpatch['timestamp']/60/60/24)+1; // patch stats start next day
} else {
	$lastpatchday=floor($lastpatch['timestamp']/60/60/24)+1; // patch stats start next day
	$nextpatchday=$day+1;
}

$tiers=array('All', 'Diamond', 'Platinum', 'Gold', 'Silver');

if(isset($_GET['tier']) && in_array($_GET['tier'], $tiers)) $tier=$_GET['tier'];
else $tier='All';

$dayscondition="day>$lastpatchday AND day<$nextpatchday AND tier='$tier'"; // current patch

$patchid=$lastpatch['id'];
$linksquery="?patch=$patchid&tier=$tier";

$totalgames=$db->query("SELECT SUM(games) FROM stats WHERE $dayscondition")->fetch_array()[0];
$totalwins=$db->query("SELECT SUM(wins) FROM stats WHERE $dayscondition")->fetch_array()[0];

if($totalwins==0) {
	$winratebalance=1;
} else {
	$winratebalance=$totalgames/$totalwins/2;
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
$v=61;
?>
<!doctype html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Brawlmance - Brawlhalla Statistics</title>
	<meta name="description" content="Brawlmance provides Brawlhalla Statistics for legend winrates, weapon winrates, leaderboards, and more">
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
	<link rel="manifest" href="/manifest.json?v2">
	<meta name="theme-color" content="#FD9700">
	
	<link rel="stylesheet" href="/css/normalize.min.css?v=2">
	<link rel="stylesheet" href="/css/main.css?v=<?=$v?>">
	<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
</head>
<body>
  <div class="container" id="main">
	<header>
      <div id="menu">
		<ul>
			<li><a href="/<?=$linksquery?>"><img src="/img/logo.png" alt="Brawlmance" title="Brawlmance" /> HOME</a></li>
			<li><a href="/legends<?=$linksquery?>">LEGENDS</a></li>
			<li><a href="/weapons<?=$linksquery?>">WEAPONS</a></li>
			<li><a href="/rankings<?=$linksquery?>">RANKINGS</a></li>
			<li><a href="/about<?=$linksquery?>">ABOUT</a></li>
		</ul>
	  </div>
      <div id="aggregationstatus">
		<form method="GET" style="display:inline" id="patchform">
		<label>Patch <select name="patch" onchange="$('#patchform').submit()">
		<?
		$patches=$db->query("SELECT id FROM patches WHERE changes='1' ORDER BY timestamp DESC LIMIT 20");
		while($patch=$patches->fetch_array(true)) {
			echo "<option ",($patch['id']==$patchid ? 'selected' : ''),">$patch[id]</option>";
		}
		?>
		</select></label>
		<input type="hidden" name="tier" value="<?=$tier?>">
		</form>
		<form method="GET" style="display:inline" id="tierform">
		<input type="hidden" name="patch" value="<?=$patchid?>">
		<label><select name="tier" onchange="$('#tierform').submit()">
		<?
		foreach($tiers as $tiername) {
			echo "<option ",($tiername==$tier ? 'selected' : ''),">$tiername</option>";
		}
		?>
		</select></label>
		</form>
		<span id="n_analyzed">Games analyzed: <?=number_format($totalgames)?></span>
	  </div>
	</header>
	<?
	if($totalgames<200000) {
		?>
		  <div id="aggregating_warning">
		   WARNING: We don't have enough data yet
		  </div>
		<?
	}
	?>
	<div id="content">