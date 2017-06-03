<?
include('header.php');
?>
<p>Data collected from Gold 3 to Top 1</p>
<p>Random fact: 
	<?
	switch(rand(1,5)) {
		case 1: echo round($db->query("SELECT SUM(wins)/SUM(games)*$winratebalance FROM stats WHERE $dayscondition")->fetch_array()[0]*1000)/10 ."% average winrate"; break;
		case 2: echo round($db->query("SELECT SUM(kounarmed)/SUM(kos) FROM stats WHERE $dayscondition")->fetch_array()[0]*1000)/10 ."% kos made unarmed"; break;
		case 4: echo round($db->query("SELECT (SUM(matchtime)-SUM(timeheldweaponone)-SUM(timeheldweapontwo)) / SUM(matchtime) FROM stats WHERE $dayscondition")->fetch_array()[0]*1000)/10 ."% of time unarmed"; break;
		default: echo round($db->query("SELECT SUM(damagegadgets)/SUM(games) FROM stats WHERE $dayscondition")->fetch_array()[0]*10)/10 ." average damage made with gadgets per game"; break;
	}
	?></p>
<p>Made with love by <a href="https://balbona.me">NiciusB</a></p>
<p>Other cool brawlhalla fansites (and other things): <a href="https://brawldb.com/">BrawlDB</a>, <a href="http://brawlspot.com/">Brawlspot</a>, <a href="http://brawlleague.com/">BrawlLeague</a>, <a href="https://www.reddit.com/r/Brawlhalla/comments/4f5em8/brawlhallapingchecker_check_your_ping_to_the/">Ping Check utility for Windows</a>, <a href="https://www.reddit.com/r/Brawlhalla/comments/4fudqw/all_brawlhalla_exclusives/">All brawlhalla skins reddit post</a></p>
<p>This project is open source! Check it out at <a href="https://github.com/NiciusB/BrawlmanceReloaded">Github</a></p>
<?
include('footer.php');
?>