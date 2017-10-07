<?

function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

$legend_id=isset($_GET['legend']) ? intval($_GET['legend']) : false;

?>
<form method="GET" id="rankingform">
<label><select name="legend" onchange="$('#rankingform').submit()">
<option value="0">Best 100 players</option>
<?
$legends=$db->query("SELECT legend_id, bio_name FROM legends ORDER BY bio_name");
while($legend=$legends->fetch_array(true)) {
	echo "<option value='$legend[legend_id]' ",($legend['legend_id']==$legend_id ? 'selected' : ''),">$legend[bio_name] masters</option>";
}
?>
</select></label>
</form>
<?

if(!$legend_id) {
	?>
	<div style="overflow: auto;">
	<table id="ranking">
	<tr>
		<th>Rank</th>
		<th>Player</th>
		<th>Rating</th>
		<th>Most played legends</th>
	</tr>
	<?
	$players=$db->query("SELECT *, (SELECT bio_name FROM legends WHERE legend_id=players.legend1) as legend1name, (SELECT bio_name FROM legends WHERE legend_id=players.legend2) as legend2name, (SELECT bio_name FROM legends WHERE legend_id=players.legend3) as legend3name FROM players ORDER BY rank LIMIT 100");
	while($leader=$players->fetch_array()) {
		?>
		<tr>
			<td><?=ordinal($leader['rank'])?></td>
			<td><a href="/search?brawlhalla_id=<?=$leader['brawlhalla_id']?>"><?=htmlentities($leader['name'])?></a><p class="region"><?=$leader['region']?></p></td>
			<td><p><?=$leader['rating']?> elo</p><p><span class="wins"><?=$leader['wins']?>W</span> <span class="losses"><?=$leader['games']-$leader['wins']?>L</p></div></td>
			<td>
				<a href="/legends<?=$linksquery?>#<?=legendName2divId($leader['legend1name'])?>"><img class="lgnd" src="/img/legends/<?=$leader['legend1']?>.png"></a>
				<a href="/legends<?=$linksquery?>#<?=legendName2divId($leader['legend2name'])?>"><img class="lgnd" src="/img/legends/<?=$leader['legend2']?>.png"></a>
				<a href="/legends<?=$linksquery?>#<?=legendName2divId($leader['legend3name'])?>"><img class="lgnd" src="/img/legends/<?=$leader['legend3']?>.png"></a>
			</td>
		</tr>
		<?
	}
	?>
	</table>
	</div>
	<?
} else {
	?>
	<div style="overflow: auto;">
	<table id="ranking">
	<tr>
		<th>Rank</th>
		<th>Player</th>
		<th>Rating</th>
		<th>Mastery</th>
	</tr>
	<?
	$players=$db->query("SELECT playerlegends.brawlhalla_id, players.name, players.region, players.rating, players.wins, players.games, playerlegends.level, playerlegends.xp FROM playerlegends 
	LEFT JOIN players ON players.brawlhalla_id=playerlegends.brawlhalla_id WHERE playerlegends.legend_id=$legend_id ORDER BY playerlegends.xp DESC LIMIT 50");
	$rank=1;
	while($leader=$players->fetch_array()) {
		if(empty($leader['name'])) $leader['name']='Unknown player';
		?>
		<tr>
			<td><?=ordinal($rank++)?></td>
			<td><a href="/search?brawlhalla_id=<?=$leader['brawlhalla_id']?>"><?=htmlentities($leader['name'])?></a><p class="region"><?=$leader['region']?></p></td>
			<td><p><?=$leader['rating']?> elo</p><p><span class="wins"><?=$leader['wins']?>W</span> <span class="losses"><?=$leader['games']-$leader['wins']?>L</p></div></td>
			<td>
				<p>Level <?=$leader['level']?></p>
				<p><?=$leader['xp']?> XP</p>
			</td>
		</tr>
		<?
	}
	?>
	</table>
	</div>
	<?
}