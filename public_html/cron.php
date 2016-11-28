<?
if(isset($_SERVER['REMOTE_ADDR'])) {echo "Nah"; exit;} // don't allow people to run the cron from the broswer. You could allow your ip for testing

include('header.php');

$db->query("DELETE FROM playerlegends WHERE day<$day-3"); // Delete players not seen in 3 days, so if they come back from later patches, stats are not fucked up

$apicalls=0;
$realapicalls=0;
function api_call($url) {
	global $apicalls, $realapicalls;
	$apicalls++;
	$realapicalls++;
	if($apicalls>=8) { sleep(1); $apicalls=1; }// Don't be too greedy about the 10 request/second limit
	$post=array();
    $defaults = array( 
        CURLOPT_POST => 1, 
        CURLOPT_HEADER => 0, 
        CURLOPT_URL => 'https://api.brawlhalla.com/'.$url.'?api_key='.$BRAWLHALLAAPIKEY,
        CURLOPT_FRESH_CONNECT => 1, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_FORBID_REUSE => 1, 
        CURLOPT_TIMEOUT => 4, 
        CURLOPT_POSTFIELDS => http_build_query($post) 
    ); 

    $ch = curl_init(); 
    curl_setopt_array($ch, $defaults); 
    if( ! $result = curl_exec($ch)) 
    { 
        trigger_error(curl_error($ch)); 
    } 
    curl_close($ch);
    $return=json_decode($result, true);
	if(isset($return['error'])) {
		var_dump($return);
		if($return['error']['code']==429) exit; // Too many requests
	}
	return $return;
}
function playerLegendToDB($brawlhalla_id, $legend, $day) {
	global $db;
	$db->query("DELETE FROM playerlegends WHERE brawlhalla_id='$brawlhalla_id' AND legend_id='$legend[legend_id]' AND day='$day'");
	$db->query("INSERT INTO playerlegends (brawlhalla_id, legend_id, day, damagedealt, damagetaken, kos, falls, suicides, teamkos, matchtime, games, wins, damageunarmed, damagethrownitem, damageweaponone, damageweapontwo, damagegadgets, kounarmed, kothrownitem, koweaponone, koweapontwo, kogadgets, timeheldweaponone, timeheldweapontwo) VALUES ('$brawlhalla_id','$legend[legend_id]', '$day','$legend[damagedealt]','$legend[damagetaken]','$legend[kos]','$legend[falls]','$legend[suicides]','$legend[teamkos]','$legend[matchtime]','$legend[games]','$legend[wins]','$legend[damageunarmed]','$legend[damagethrownitem]','$legend[damageweaponone]','$legend[damageweapontwo]','$legend[damagegadgets]','$legend[kounarmed]','$legend[kothrownitem]','$legend[koweaponone]','$legend[koweapontwo]','$legend[kogadgets]','$legend[timeheldweaponone]','$legend[timeheldweapontwo]')");
}
function statsToDB($legend, $day) {
	global $db;
	$isindb=$db->query("SELECT 1 FROM stats WHERE legend_id='$legend[legend_id]' AND day='$day'");
	if($isindb->num_rows>0) {
		$db->query("UPDATE stats SET damagedealt=damagedealt+$legend[damagedealt], damagetaken=damagetaken+$legend[damagetaken], kos=kos+$legend[kos], falls=falls+$legend[falls], suicides=suicides+$legend[suicides], teamkos=teamkos+$legend[teamkos], matchtime=matchtime+$legend[matchtime], games=games+$legend[games], wins=wins+$legend[wins], damageunarmed=damageunarmed+$legend[damageunarmed], damagethrownitem=damagethrownitem+$legend[damagethrownitem], damageweaponone=damageweaponone+$legend[damageweaponone], damageweapontwo=damageweapontwo+$legend[damageweapontwo], damagegadgets=damagegadgets+$legend[damagegadgets], kounarmed=kounarmed+$legend[kounarmed], kothrownitem=kothrownitem+$legend[kothrownitem], koweaponone=koweaponone+$legend[koweaponone], koweapontwo=koweapontwo+$legend[koweapontwo], kogadgets=kogadgets+$legend[kogadgets], timeheldweaponone=timeheldweaponone+$legend[timeheldweaponone], timeheldweapontwo=timeheldweapontwo+$legend[timeheldweapontwo] WHERE legend_id='$legend[legend_id]' AND day='$day'");
	} else {
		$db->query("INSERT INTO stats (legend_id, day) VALUES ('$legend[legend_id]', '$day')");
		statsToDB($legend, $day);
	}
}
$a = array('us-w', 'us-e', 'brz', 'eu', 'sea', 'aus');
$time=time()+3*60*60; // trying to get about 10pm in every server
$region = $a[floor($time/60/5/48)%6]; // rotating
$page = floor($time/60/5)%48+1+5; // we can do up to 48 pages per region per day, with 1 page every 5 mins
$ranking=api_call('rankings/1v1/'.$region.'/'.$page);

if(empty($ranking['error'])) { // RATE LIMIT? OR API DOWN
	$n=0;
	foreach($ranking as $user) {
		$n++;
		$user=api_call('player/'.$user['brawlhalla_id'].'/stats');
		if(isset($user['legends'])) { // something went wrong D:
			foreach($user['legends'] as $legend) {
				$isindb=$db->query("SELECT 1 FROM legends WHERE legend_id=$legend[legend_id]");
				if($isindb->num_rows==0) {
					$newlegend=api_call("legend/$legend[legend_id]");
					if(isset($newlegend['legend_id'])) {
						$db->query("INSERT INTO legends (legend_id, legend_name_key, bio_name, weapon_one, weapon_two, strength, dexterity, defense, speed) VALUES ('$newlegend[legend_id]','$newlegend[legend_name_key]','$newlegend[bio_name]','$newlegend[weapon_one]','$newlegend[weapon_two]','$newlegend[strength]','$newlegend[dexterity]','$newlegend[defense]','$newlegend[speed]')");
					}
				}
				
				$oldlegend=$db->query("SELECT * FROM playerlegends WHERE brawlhalla_id=$user[brawlhalla_id] AND legend_id=$legend[legend_id]");
				if($oldlegend->num_rows>0) {
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
					statsToDB($oldlegend, $day);
				} 
				playerLegendToDB($user['brawlhalla_id'], $legend, $day);
			}
		}
	}
}

echo $realapicalls;