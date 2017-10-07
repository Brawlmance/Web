<?php

ini_set('display_errors', 1);

include('keys.php');
$db = new mysqli($db_host, $db_user, $db_password, $db_name);
if ($db->connect_errno) {
    printf("DB is down: %s\n", $db->connect_error);
    exit();
}
$db->set_charset("utf8mb4");

if(isset($_GET['patch'])) {
	$currentpatch=$db->query("SELECT MIN(timestamp) as timestamp, id FROM patches WHERE changes='1' AND id='".$db->real_escape_string($_GET['patch'])."'")->fetch_array();
	if(isset($currentpatch['id'])) {
		$patchid=$currentpatch['id'];
		$currentpatch=$currentpatch['timestamp'];
		$nextpatch=$db->query("SELECT MIN(timestamp) as timestamp FROM patches WHERE changes='1' AND timestamp>$currentpatch")->fetch_array();
		if(empty($nextpatch['timestamp'])) $nextpatch=time(); else $nextpatch=$nextpatch['timestamp'];
	}
}

if(empty($patchid)) {
	$currentpatch=$db->query("SELECT timestamp, id FROM patches WHERE changes='1' AND timestamp<".time()."-60*60*24*2 ORDER BY timestamp DESC LIMIT 1")->fetch_array();
	$patchid=$currentpatch['id'];
	$currentpatch=$currentpatch['timestamp'];
	$nextpatch=time();
}

$currentpatchday=floor($currentpatch/60/60/24)+1; // patch stats start next day
$nextpatchday=floor($nextpatch/60/60/24)+1; // patch stats start next day

$tiers=array('All', 'Diamond', 'Platinum', 'Gold', 'Silver');
if(isset($_GET['tier']) && in_array($_GET['tier'], $tiers)) $tier=$_GET['tier']; else $tier='All';
$linksquery="?patch=$patchid&tier=$tier";

$dayscondition="day>$currentpatchday AND day<$nextpatchday AND tier='$tier'"; // current patch

$totalgames=$db->query("SELECT SUM(games) FROM stats WHERE $dayscondition")->fetch_array()[0];
$totalwins=$db->query("SELECT SUM(wins) FROM stats WHERE $dayscondition")->fetch_array()[0];

if($totalwins==0) {
	$winratebalance=1;
} else {
	$winratebalance=$totalgames/$totalwins/2; // Because we're not counting lower elos, we will have more wins than losses. We use this variable to normalize the winrates
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

function includeStylesheet($name) {
	global $v;
	echo '<link rel="stylesheet" href="/css/', $name, '.css?v=', $v, '">';
}

function echoHiddenInputsForQueryStrings($skip = array()) {
	foreach($_GET as $name => $value) {
		if(in_array($name, $skip)) continue;
		$name = htmlspecialchars($name);
		$value = htmlspecialchars($value);
		echo '<input type="hidden" name="'. $name .'" value="'. $value .'">';
	}
}

function time_elapsed_string($datetime, $full = false) {
	$now = new DateTime;
	$ago = new DateTime($datetime);
	$diff = $now->diff($ago);

	$diff->w = floor($diff->d / 7);
	$diff->d -= $diff->w * 7;

	$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
	);
	foreach ($string as $k => &$v) {
			if ($diff->$k) {
					$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
					unset($string[$k]);
			}
	}

	if (!$full) $string = array_slice($string, 0, 1);
	return $string ? implode(', ', $string) . ' ago' : 'just now';
}