<?

function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

$legend_id = isset($_REQUEST['legend']) ? intval($_REQUEST['legend']) : false;
if (empty($_REQUEST['sort'])) $_REQUEST['sort'] = 'mastery';
$sort = $_REQUEST['sort'];

?>
<form method="GET" id="rankingform">
<label><select name="legend" onchange="$('#rankingform').submit()">
<option selected disabled>Select legend</option>
<?
$legends=$db->query("SELECT legend_id, bio_name FROM legends ORDER BY bio_name");
while($legend=$legends->fetch_array(true)) {
	echo "<option value='$legend[legend_id]' ",($legend['legend_id']==$legend_id ? 'selected' : ''),">$legend[bio_name]</option>";
}
?>
</select></label>
<select name="sort" onchange="$('#rankingform').submit()">
    <option value="mastery" <? if ($sort === 'mastery') echo 'selected'; ?>>Mastery</option>
    <option value="elo" <? if ($sort === 'elo') echo 'selected'; ?>>Elo</option>
    <option value="peak_elo" <? if ($sort === 'peak_elo') echo 'selected'; ?>>Peak Elo</option>
</select>
</form>
<?

if ($sort === 'mastery') {
	?>
	<div style="overflow: auto;">
	<table id="ranking">
	<tr>
		<th>Rank</th>
		<th>Player</th>
		<th>Player Rating</th>
		<th>Legend Mastery</th>
	</tr>
	<?
    $players = $db->query("SELECT player_legends.brawlhalla_id, players.name, players.region, players.rating, players.wins, players.games, player_legends.level, player_legends.xp FROM player_legends 
                        LEFT JOIN players ON players.brawlhalla_id=player_legends.brawlhalla_id
                        WHERE player_legends.legend_id='$legend_id' ORDER BY player_legends.xp DESC LIMIT 50");
	$rank = 1;
	while($leader = $players->fetch_assoc()) {
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
} else {
	?>
	<div style="overflow: auto;">
	<table id="ranking">
	<tr>
		<th>Rank</th>
		<th>Player</th>
		<th>Legend Rating</th>
		<th>Legend Mastery</th>
	</tr>
	<?
	$orderField = $sort === 'elo' ? 'rating' : 'peak_rating';
    $players = $db->query("SELECT player_ranked_legends.brawlhalla_id, players.name, players.region, player_ranked_legends.$orderField, player_ranked_legends.wins, player_ranked_legends.games, player_legends.level, player_legends.xp FROM player_ranked_legends 
                    LEFT JOIN players ON players.brawlhalla_id=player_ranked_legends.brawlhalla_id
                    LEFT JOIN player_legends ON player_legends.brawlhalla_id=player_ranked_legends.brawlhalla_id AND player_legends.legend_id=player_ranked_legends.legend_id
                    WHERE player_ranked_legends.legend_id='$legend_id' ORDER BY player_ranked_legends.$orderField DESC LIMIT 50");
	$rank = 1;
	while($leader = $players->fetch_assoc()) {
		if(empty($leader['name'])) $leader['name']='Unknown player';
		?>
		<tr>
			<td><?=ordinal($rank++)?></td>
			<td><a href="/search?brawlhalla_id=<?=$leader['brawlhalla_id']?>"><?=htmlentities($leader['name'])?></a><p class="region"><?=$leader['region']?></p></td>
			<td><p><?=$leader[$orderField] . ' ' . ($sort === 'elo' ? 'elo' : 'peak elo')?></p><p><span class="wins"><?=$leader['wins']?>W</span> <span class="losses"><?=$leader['games']-$leader['wins']?>L</p></div></td>
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