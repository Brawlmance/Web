<?
if (isset($_SERVER['REMOTE_ADDR'])) {
    echo "Nah";
    exit;
} // don't allow people to run the cron from the broswer. You could allow your ip for testing

set_time_limit(60);
include('config.php');

$day=floor(time()/60/60/24);

$steampatches=json_decode(file_get_contents("https://api.steampowered.com/ISteamNews/GetNewsForApp/v0002/?appid=291550&count=30&maxlength=300&format=json"), true);

if (isset($steampatches['appnews']) && isset($steampatches['appnews']['newsitems'])) {
    foreach ($steampatches['appnews']['newsitems'] as $patch) {
        if (strpos($patch['title'], 'Patch') !== false || strpos($patch['title'], 'Update') !== false) {
            preg_match( '/[0-9]+\.[0-9]+\.[0-9]+/', $patch['title'], $submatches );
            preg_match( '/[0-9]+\.[0-9]+/', $patch['title'], $matches );
            if (sizeof($submatches)>0) {
                $lastEl = array_values(array_slice($submatches, -1))[0];
                $patchid=$lastEl;
            } elseif (sizeof($matches)>0) {
                $lastEl = array_values(array_slice($matches, -1))[0];
                $patchid=$lastEl;
            } else {
                $patchid='?';
            }
            $patch_timestamp_exists=$db->query("SELECT 1 FROM patches WHERE timestamp='".$patch['date']."'")->num_rows>0;
            $patchexists=$db->query("SELECT 1 FROM patches WHERE id='".$patchid."'")->num_rows>0;
            if (!$patch_timestamp_exists) {
                $db->query("INSERT INTO patches (id, timestamp, changes) VALUES ('".$patchid."', '".$patch['date']."', '". ($patchexists ? '0' : '1') ."')");
            }
        }
    }
}

$db->query("DELETE FROM players WHERE lastupdated<".time()."-60*60*24*7");
$db->query("DELETE FROM playerlegends WHERE day<=$day-3"); // Delete players not seen in 3 days, so if they come back from later patches, stats are not fucked up
$apicalls=0;
$realapicalls=0;
function api_call($url)
{
    global $apicalls, $realapicalls, $BRAWLHALLAAPIKEY;
    $apicalls++;
    $realapicalls++;
    if ($apicalls>=8) {
        sleep(1);
        $apicalls=1;
    } // Don't be too greedy about the 10 request/second limit
    $post=array();
    $defaults = array(
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_URL => 'https://api.brawlhalla.com/'.$url.'?api_key='.$BRAWLHALLAAPIKEY,
                CURLOPT_ENCODING => 'UTF-8',
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FORBID_REUSE => 1,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_POSTFIELDS => http_build_query($post),
                CURLOPT_USERAGENT => 'BrawlmanceBot'
    );

    $ch = curl_init();
    curl_setopt_array($ch, $defaults);
    if (! $result = curl_exec($ch)) {
        trigger_error(curl_error($ch));
    }
    curl_close($ch);
    $return=json_decode($result, true);
    if (isset($return['error'])) {
        var_dump($return);
        if ($return['error']['code']==429) {
            sleep(2); // Too many requests
        }
    }
    return $return;
}

function statsToDB($legend, $tier, $elo, $day) {
    global $db;
    $isindb=$db->query("SELECT 1 FROM stats WHERE legend_id='$legend[legend_id]' AND day='$day' AND tier='$tier'");
    if ($isindb->num_rows>0) {
        $db->query("UPDATE stats SET damagedealt=damagedealt+$legend[damagedealt], damagetaken=damagetaken+$legend[damagetaken], kos=kos+$legend[kos],
				falls=falls+$legend[falls], suicides=suicides+$legend[suicides], teamkos=teamkos+$legend[teamkos], matchtime=matchtime+$legend[matchtime],
				games=games+$legend[games], wins=wins+$legend[wins], elo=elo+$elo, damageunarmed=damageunarmed+$legend[damageunarmed],
				damagethrownitem=damagethrownitem+$legend[damagethrownitem], damageweaponone=damageweaponone+$legend[damageweaponone],
				damageweapontwo=damageweapontwo+$legend[damageweapontwo], damagegadgets=damagegadgets+$legend[damagegadgets], kounarmed=kounarmed+$legend[kounarmed],
				kothrownitem=kothrownitem+$legend[kothrownitem], koweaponone=koweaponone+$legend[koweaponone], koweapontwo=koweapontwo+$legend[koweapontwo],
				kogadgets=kogadgets+$legend[kogadgets], timeheldweaponone=timeheldweaponone+$legend[timeheldweaponone],
				timeheldweapontwo=timeheldweapontwo+$legend[timeheldweapontwo] WHERE legend_id='$legend[legend_id]' AND day='$day' AND tier='$tier'");
    } else {
        $db->query("INSERT INTO stats (legend_id, day, tier) VALUES ('$legend[legend_id]', '$day', '$tier')");
        statsToDB($legend, $tier, $elo, $day);
    }
}

function updatePlayer($rankingplayer, $player) {
    global $db;
    if (isset($player['legends'])) {
        $toplegends = array();
        foreach ($player['legends'] as $key => $row) {
            $toplegends[$key] = $row['xp'];
        }
        array_multisort($toplegends, SORT_DESC, $player['legends']);
        $legend1=$player['legends'][0]['legend_id'];
        if (sizeof($player['legends'])>1) {
            $legend2=$player['legends'][1]['legend_id'];
        } else {
            $legend2=0;
        }
        if (sizeof($player['legends'])>2) {
            $legend3=$player['legends'][2]['legend_id'];
        } else {
            $legend3=0;
        }
        $db->query("DELETE FROM players WHERE brawlhalla_id='$rankingplayer[brawlhalla_id]'");
        $rankingplayer['name']=$db->real_escape_string(utf8_decode($rankingplayer['name']));
        $db->query("INSERT INTO players (brawlhalla_id, name, xp, level, rank, tier, games, wins, rating, region, legend1, legend2, legend3, lastupdated) VALUES
				('$rankingplayer[brawlhalla_id]', '$rankingplayer[name]', '$player[xp]', '$player[level]', '$rankingplayer[rank]', '$rankingplayer[tier]',
				'$rankingplayer[games]', '$rankingplayer[wins]', '$rankingplayer[rating]', '$rankingplayer[region]', '$legend1', '$legend2', '$legend3', '".time()."')");
        
        foreach ($player['legends'] as $legend) {
            if ($legend['legend_id']==17) {
                continue; // it doesn't actually exist
            }
            updateLegend($rankingplayer, $player, $legend);
        }
                
        if (isset($player['clan'])) {
            updateClan($player['clan'], $player['brawlhalla_id']);
        }
    }
}

function updateLegend($rankingplayer, $player, $legend) {
    global $db, $day;
    $oldlegend=$db->query("SELECT * FROM playerlegends WHERE brawlhalla_id='$player[brawlhalla_id]' AND legend_id='$legend[legend_id]' order by day desc limit 1"); // order by day desc limit 1 porque soy tonto, borrar mañana
    if ($oldlegend->num_rows>0) {
        $oldlegend=$oldlegend->fetch_array();
        $oldlegend['damagedealt']=$legend['damagedealt']-$oldlegend['damagedealt'];
        $oldlegend['damagetaken']=$legend['damagetaken']-$oldlegend['damagetaken'];
        $oldlegend['kos']=$legend['kos']-$oldlegend['kos'];
        $oldlegend['falls']=$legend['falls']-$oldlegend['falls'];
        $oldlegend['suicides']=$legend['suicides']-$oldlegend['suicides'];
        $oldlegend['teamkos']=$legend['teamkos']-$oldlegend['teamkos'];
        $oldlegend['matchtime']=$legend['matchtime']-$oldlegend['matchtime'];
        $oldlegend['games']=$legend['games']-$oldlegend['games'];
        $oldlegend['wins']=$legend['wins']-$oldlegend['wins'];
        $oldlegend['damageunarmed']=$legend['damageunarmed']-$oldlegend['damageunarmed'];
        $oldlegend['damagethrownitem']=$legend['damagethrownitem']-$oldlegend['damagethrownitem'];
        $oldlegend['damageweaponone']=$legend['damageweaponone']-$oldlegend['damageweaponone'];
        $oldlegend['damageweapontwo']=$legend['damageweapontwo']-$oldlegend['damageweapontwo'];
        $oldlegend['damagegadgets']=$legend['damagegadgets']-$oldlegend['damagegadgets'];
        $oldlegend['kounarmed']=$legend['kounarmed']-$oldlegend['kounarmed'];
        $oldlegend['kothrownitem']=$legend['kothrownitem']-$oldlegend['kothrownitem'];
        $oldlegend['koweaponone']=$legend['koweaponone']-$oldlegend['koweaponone'];
        $oldlegend['koweapontwo']=$legend['koweapontwo']-$oldlegend['koweapontwo'];
        $oldlegend['kogadgets']=$legend['kogadgets']-$oldlegend['kogadgets'];
        $oldlegend['timeheldweaponone']=$legend['timeheldweaponone']-$oldlegend['timeheldweaponone'];
        $oldlegend['timeheldweapontwo']=$legend['timeheldweapontwo']-$oldlegend['timeheldweapontwo'];
        $oldlegend['xp']=$legend['xp'];
        $oldlegend['level']=$legend['level'];
        
        if ($oldlegend['games']>0) {
            statsToDB($oldlegend, 'all', $rankingplayer['rating']*$oldlegend['games'], $day);
            statsToDB($oldlegend, preg_replace('/(.*) (\d)/', '$1', $rankingplayer['tier']), $rankingplayer['rating']*$oldlegend['games'], $day);
        }
        
        $db->query("DELETE FROM playerlegends WHERE brawlhalla_id='$player[brawlhalla_id]' AND legend_id='$legend[legend_id]'");
    } else { // maybe its a new legend
        $isindb=$db->query("SELECT 1 FROM legends WHERE legend_id=$legend[legend_id]");
        if ($isindb->num_rows==0) {
            $newlegend=api_call("legend/$legend[legend_id]");
            if (isset($newlegend['legend_id'])) {
                $db->query("INSERT INTO legends (legend_id, legend_name_key, bio_name, weapon_one, weapon_two, strength, dexterity, defense, speed) VALUES
								('$newlegend[legend_id]','".utf8_decode($newlegend['legend_name_key'])."','$newlegend[bio_name]','$newlegend[weapon_one]','$newlegend[weapon_two]',
								'$newlegend[strength]','$newlegend[dexterity]','$newlegend[defense]','$newlegend[speed]')");
            }
        }
    }
    $db->query("INSERT INTO playerlegends (brawlhalla_id, legend_id, day, damagedealt, damagetaken, kos, falls, suicides, teamkos, matchtime, games, wins,
		damageunarmed, damagethrownitem, damageweaponone, damageweapontwo, damagegadgets, kounarmed, kothrownitem, koweaponone, koweapontwo, kogadgets,
		timeheldweaponone, timeheldweapontwo, xp, level) VALUES
		('$player[brawlhalla_id]','$legend[legend_id]', '$day','$legend[damagedealt]','$legend[damagetaken]','$legend[kos]','$legend[falls]','$legend[suicides]',
		'$legend[teamkos]','$legend[matchtime]','$legend[games]','$legend[wins]','$legend[damageunarmed]','$legend[damagethrownitem]','$legend[damageweaponone]',
		'$legend[damageweapontwo]','$legend[damagegadgets]','$legend[kounarmed]','$legend[kothrownitem]','$legend[koweaponone]','$legend[koweapontwo]',
		'$legend[kogadgets]','$legend[timeheldweaponone]','$legend[timeheldweapontwo]','$legend[xp]','$legend[level]')");
}

function updateClan($clan, $brawlhalla_id) {
    global $db;
    $clan['clan_name']=$db->real_escape_string(utf8_decode($clan['clan_name']));
    $clan_exists = $db->query("SELECT 1 FROM clans WHERE clan_id='$clan[clan_id]'");
    if ($clan_exists->num_rows > 0) {
        $db->query("UPDATE clans SET clan_name = '$clan[clan_name]', clan_xp = '$clan[clan_xp]' WHERE clan_id='$clan[clan_id]'");
    } else {
        $db->query("INSERT INTO clans (clan_id, clan_name, clan_xp) VALUES ('$clan[clan_id]', '$clan[clan_name]', '$clan[clan_xp]')");
    }
    $db->query("DELETE FROM clan_members WHERE brawlhalla_id='$brawlhalla_id'");
    $db->query("INSERT INTO clan_members (clan_id, brawlhalla_id, personal_xp) VALUES ('$clan[clan_id]', '$brawlhalla_id', '$clan[personal_xp]')");
}

$cronfrequency=1440; // CRON EXECTIONS PER DAY

// I'm are calling this script once per minute, because my limit is greater than the default 180req/15min
// Each cron call to the cron.php file uses 50 profile req + 1 for rankings = 51req
// We can do up to $cronfrequency pages per day, being the first page 1, and the last one $cronfrequency+1

$page = 1 + (floor(time() / (24*60*60 / $cronfrequency)) % $cronfrequency); // Magic calculation of the page we're at
$ranking=api_call('rankings/1v1/all/'.$page);

if (empty($ranking['error'])) { // RATE LIMIT? OR API DOWN
    $n=0;
    foreach ($ranking as $key => $rankingplayer) {
        $n++;
        $player=api_call('player/'.$rankingplayer['brawlhalla_id'].'/stats');
        updatePlayer($rankingplayer, $player);
    }
}

echo $realapicalls.' api calls, page '.$page;
