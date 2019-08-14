<?php

ini_set('display_errors', 1);
date_default_timezone_set('America/New_York');

include('keys.php');
$db = new mysqli($db_host, $db_user, $db_password, $db_name);
if ($db->connect_errno) {
    printf("DB is down: %s\n", $db->connect_error);
    exit();
}
$db->set_charset("utf8mb4");

// Set global tier variable, based on $_GET['tier']
$tiers=array('All', 'Diamond', 'Platinum', 'Gold', 'Silver');
if (isset($_GET['tier']) && in_array($_GET['tier'], $tiers)) $tier=$_GET['tier']; else $tier='All';

// Set global patchid variable, based on $_GET['patch']. If invalid, use latest patch
if (isset($_GET['patch'])) {
	$currentpatch = $db->query("SELECT id FROM patches WHERE changes='1' AND id='".$db->real_escape_string($_GET['patch'])."'")->fetch_assoc();
	if (isset($currentpatch['id'])) $patchid = $currentpatch['id'];
}
if (empty($patchid)) {
	$currentpatch = $db->query("SELECT id FROM patches WHERE changes='1' AND timestamp<".time()."-60*60*24*2 ORDER BY timestamp DESC LIMIT 1")->fetch_assoc();
	$patchid = $currentpatch['id'];
}

// Set global misc variables
$dayscondition = getPatchDaysCondition($patchid, $tier);
$linksquery = "?patch=$patchid&tier=$tier";
$header_totalgames = $db->query("SELECT SUM(games) FROM stats WHERE $dayscondition")->fetch_array()[0];

function getPreviousPatchID($patchID) {
    global $db;
    $escapedPatchID = $db->real_escape_string($patchID);
	$previouspatch = $db->query("SELECT id FROM patches WHERE changes='1' AND timestamp<(SELECT timestamp FROM patches WHERE changes='1' AND id='$escapedPatchID' LIMIT 1) ORDER BY timestamp DESC LIMIT 1")->fetch_assoc();
	return $previouspatch['id'];
}

function getPatchDaysCondition($patch, $tier) {
    global $db;
    $escapedPatch = $db->real_escape_string($patch);
	$currentpatch = $db->query("SELECT MIN(timestamp) as timestamp, id FROM patches WHERE changes='1' AND id='$escapedPatch'")->fetch_assoc();
	if(!isset($currentpatch['id'])) throw new Exception('Invalid patch');

	$currentpatch = $currentpatch['timestamp'];
	$nextpatch = $db->query("SELECT MIN(timestamp) as timestamp FROM patches WHERE changes='1' AND timestamp>$currentpatch")->fetch_assoc();
	if (empty($nextpatch['timestamp'])) $nextpatch = time();
	else  $nextpatch = $nextpatch['timestamp'];

    $currentpatchday = floor($currentpatch / 60 / 60 / 24) + 1; // patch stats start next day
    $nextpatchday = floor($nextpatch / 60 / 60 / 24) + 1; // patch stats start next day

    return "(day>$currentpatchday AND day<$nextpatchday AND tier='$tier')";
}

function getPatchGlobalInfo($dayscondition) {
    global $db;
    $totalgames = $db->query("SELECT SUM(games) FROM stats WHERE $dayscondition")->fetch_array()[0];
    $totalwins = $db->query("SELECT SUM(wins) FROM stats WHERE $dayscondition")->fetch_array()[0];
    
    if ($totalwins == 0) {
    	$winratebalance = 1;
    } else {
        // Because we're not counting 100% of the matches, and only higher elos, we will have more wins than losses. We use this variable to normalize the winrates
    	$winratebalance = $totalgames / $totalwins / 2;
    }
    return [
        'winratebalance' => $winratebalance,
        'totalgames' => $totalgames,
    ];
}

function _formatRawStatsFromQuery($legendStats, $dayscondition) {
    $patchGlobalInfo = getPatchGlobalInfo($dayscondition);

    if ($legendStats['games'] == 0 || $legendStats['damagedealt'] == 0 || $legendStats['matchtime'] == 0) return null;
    return [
        'games' => $legendStats['games'],
        'playrate' => number_format($legendStats['games'] / $patchGlobalInfo['totalgames'] * 100, 2),
        'winrate' => number_format($legendStats['winrate'] * $patchGlobalInfo['winratebalance'] * 100, 2),
        'suicides' => number_format($legendStats['suicides'], 2), 
        'damagedealt' => $legendStats['damagedealt'],
        'damagedealt_unarmed' => $legendStats['damagedealt_unarmed'],
        'damagedealt_gadgets' => $legendStats['damagedealt_gadgets'],
        'damagedealt_weaponone' => $legendStats['damagedealt_weaponone'],
        'damagedealt_weapontwo' => $legendStats['damagedealt_weapontwo'],
        'damagetaken' => number_format($legendStats['damagetaken']),
        'matchtime' => $legendStats['matchtime'],
        'matchtime_weaponone' => $legendStats['matchtime_weaponone'],
        'matchtime_weapontwo' => $legendStats['matchtime_weapontwo'],
        'matchtime_unarmed' => $legendStats['matchtime'] - $legendStats['matchtime_weaponone'] - $legendStats['matchtime_weapontwo'],
    ];
}

function getLegendData($legendID) {
    global $db;
    $legends = $db->query("SELECT legend_id, bio_name, strength, dexterity, defense, speed, weapon_one, weapon_two FROM legends WHERE legend_id='$legendID'");
    $legend = $legends->fetch_assoc();
    return $legend;
}

function getAllLegendStats($patch, $tier) {
    global $db;

    $dayscondition = getPatchDaysCondition($patch, $tier);
    $legendStatsQuery = $db->query("SELECT
        legend_id,
        SUM(games) as games,
        SUM(wins)/SUM(games) as winrate,
        SUM(suicides)/SUM(games) as suicides,
        SUM(damagedealt)/SUM(games) as damagedealt,
        SUM(damageunarmed)/SUM(games) as damagedealt_unarmed,
        SUM(damagegadgets)/SUM(games) as damagedealt_gadgets,
        SUM(damageweaponone)/SUM(games) as damagedealt_weaponone,
        SUM(damageweapontwo)/SUM(games) as damagedealt_weapontwo,
        SUM(damagetaken)/SUM(games) as damagetaken,
        SUM(matchtime)/SUM(games) as matchtime,
        SUM(timeheldweaponone)/SUM(games) as matchtime_weaponone,
        SUM(timeheldweapontwo)/SUM(games) as matchtime_weapontwo
    FROM stats WHERE $dayscondition GROUP BY legend_id");

    $result = [];
    while ($legendStats = $legendStatsQuery->fetch_assoc()) {
        $result[] = [
            'data' => getLegendData($legendStats['legend_id']),
            'stats' => _formatRawStatsFromQuery($legendStats, $dayscondition),    
        ];
    }
    return $result;
}

function getStats($legendID, $patch, $tier) {
    global $db;

    $dayscondition = getPatchDaysCondition($patch, $tier);
    $legendAnd = "AND legend_id = $legendID";
    $legendStatsQuery = $db->query("SELECT
        SUM(games) as games,
        SUM(wins)/SUM(games) as winrate,
        SUM(suicides)/SUM(games) as suicides,
        SUM(damagedealt)/SUM(games) as damagedealt,
        SUM(damageunarmed)/SUM(games) as damagedealt_unarmed,
        SUM(damagegadgets)/SUM(games) as damagedealt_gadgets,
        SUM(damageweaponone)/SUM(games) as damagedealt_weaponone,
        SUM(damageweapontwo)/SUM(games) as damagedealt_weapontwo,
        SUM(damagetaken)/SUM(games) as damagetaken,
        SUM(matchtime)/SUM(games) as matchtime,
        SUM(timeheldweaponone)/SUM(games) as matchtime_weaponone,
        SUM(timeheldweapontwo)/SUM(games) as matchtime_weapontwo
    FROM stats WHERE $dayscondition $legendAnd");

    $legendStats = $legendStatsQuery->fetch_assoc();
    return _formatRawStatsFromQuery($legendStats, $dayscondition);
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