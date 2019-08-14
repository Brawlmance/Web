<p>Data collected from the top ~400,000 brawlhalla players (Roughly from Silver 2 to Top 1)</p>
<p>We don't have a way to only count ranked matches (until they release the game history API), so we're counting custom and other queues, but the data should be accurate enough. I've tried a couple of times to have BMG check their stats VS mines with no luck, but they said that it should be representative</p>
<p>Random fact: 
	<?
	switch(rand(1,5)) {
		case 1: echo round($db->query("SELECT SUM(wins)/SUM(games)*$winratebalance FROM stats WHERE $dayscondition")->fetch_array()[0]*1000)/10 ."% average winrate"; break;
		case 2: echo round($db->query("SELECT SUM(kounarmed)/SUM(kos) FROM stats WHERE $dayscondition")->fetch_array()[0]*1000)/10 ."% kos made unarmed"; break;
		case 4: echo round($db->query("SELECT (SUM(matchtime)-SUM(timeheldweaponone)-SUM(timeheldweapontwo)) / SUM(matchtime) FROM stats WHERE $dayscondition")->fetch_array()[0]*1000)/10 ."% of time unarmed"; break;
		default: echo round($db->query("SELECT SUM(damagegadgets)/SUM(games) FROM stats WHERE $dayscondition")->fetch_array()[0]*10)/10 ." average damage made with gadgets per game"; break;
	}
	?></p>
<p>Made with love by <a href="https://balbona.me/">NiciusB</a></p>
<p>Other cool brawlhalla fansites (and other things): <a href="https://brawldb.com/">BrawlDB</a>, <a href="https://brawlspot.com/">Brawlspot</a>, <a href="https://www.stats.brawlhalla.fr/">Brawlhalla stats</a></p>
<p>This project is open source! Check it out at <a href="https://github.com/Brawlmance">Github</a></p>