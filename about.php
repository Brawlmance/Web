<?
include('header.php');
?>
<p>Data collected from Gold 3 to Top 1</p>
<table style="width: 80%;text-align: center;margin: 0 auto;">
<tr>
	<th>Role</th>
	<th>Legends</th>
</tr>
<?
$roles=$db->query("SELECT distinct role as role FROM legends WHERE role IS NOT NULL ORDER BY role");
while($role=$roles->fetch_array()) {
	$role=$role['role'];
	$legends=$db->query("SELECT legend_id, (SELECT bio_name FROM legends WHERE legend_id=stats.legend_id) as bio_name FROM stats WHERE legend_id in (select legend_id from legends  where role='$role') group by legend_id");
	if($legends->num_rows>0) {
		$first=true;
		?>
		<tr>
			<td><?=$rolenames[$role]?></td>
			<td><?
			while($legend=$legends->fetch_array()) {
			if($first) $first=false; else echo ", ";
			?><a href="/#<?=$legend['bio_name']?>"><?=$legend['bio_name']?></a><?
			}
			?></td>
		</tr>
		<?
	}
}
?>
</table>
<p>Random fact: 
	<?
	switch(rand(1,5)) {
		case 1: echo round($db->query("SELECT SUM(wins)/SUM(games)*$winratebalance FROM stats WHERE $dayscondition")->fetch_array()[0]*1000)/10 ."% average winrate"; break;
		case 2: echo round($db->query("SELECT SUM(kounarmed)/SUM(kos) FROM stats WHERE $dayscondition")->fetch_array()[0]*1000)/10 ."% kos made unarmed"; break;
		case 4: echo round($db->query("SELECT (SUM(matchtime)-SUM(timeheldweaponone)-SUM(timeheldweapontwo)) / SUM(matchtime) FROM stats WHERE $dayscondition")->fetch_array()[0]*1000)/10 ."% of time unarmed"; break;
		default: echo round($db->query("SELECT SUM(damagegadgets)/SUM(games) FROM stats WHERE $dayscondition")->fetch_array()[0]*10)/10 ." average damage made with gadgets per game"; break;
	}
	?></p>
<p>Contact: use the button in the header</p>
<p>Other cool brawlhalla fansites (and other things): <a href="https://brawldb.com/">BrawlDB</a>, <a href="http://brawlhalla.rocks/">brawlhalla.rocks</a>, <a href="http://brawlspot.com/">Brawlspot</a>, <a href="http://stats.brawlhalla.fr/">Brawlhalla stats</a>, <a href="https://www.reddit.com/r/Brawlhalla/comments/4fudqw/all_brawlhalla_exclusives/">All brawlhalla skins reddit post</a></p>
<p>This is kinda open source! Check it out at <a href="https://github.com/NiciusB/BrawlmanceReloaded">Github</a></p>
<?
include('footer.php');
?>