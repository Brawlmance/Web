<?
include('header.php');
?>
		<script>
		var startsortfn=function() {
			tinysort('.card',{selector:'i[data-name="name"]',attr:'data-value', order: 'asc'}); // for some reason mysql, php, and javascript sort special chars differently
		}
		</script>
		<div class="grid">
		<?
		$weapons=array();
		$weaponsquery=$db->query("SELECT weapon_one, weapon_two FROM legends GROUP BY weapon_one, weapon_one ORDER BY weapon_one, weapon_two"); // im bad at sql
		while($weapon=$weaponsquery->fetch_array()) {
			if(!in_array($weapon[0], $weapons)) $weapons[$weapon[0]]=$weapon[0];
			if(!in_array($weapon[1], $weapons)) $weapons[$weapon[1]]=$weapon[1];
		}
		foreach($weapons as $weapon) {
			$games=$db->query("SELECT SUM(games) FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_one='$weapon' OR weapon_two='$weapon') AND $dayscondition")->fetch_array()[0];
			$damagedealt=$db->query("SELECT SUM(damagedealt)/$games FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_one='$weapon' OR weapon_two='$weapon') AND $dayscondition")->fetch_array()[0];
			$matchtime=$db->query("SELECT SUM(matchtime)/$games FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_one='$weapon' OR weapon_two='$weapon') AND $dayscondition")->fetch_array()[0];
			$timeheldweaponone=$db->query("SELECT SUM(timeheldweaponone)/$games FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_one='$weapon' OR weapon_two='$weapon') AND $dayscondition")->fetch_array()[0];
			$timeheldweapontwo=$db->query("SELECT SUM(timeheldweapontwo)/$games FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_one='$weapon' OR weapon_two='$weapon') AND $dayscondition")->fetch_array()[0];
			
			$playrate=number_format($games/$totalgames*100, 2);
			$winrate=number_format($db->query("SELECT SUM(wins)/$games FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_one='$weapon' OR weapon_two='$weapon') AND $dayscondition")->fetch_array()[0]*$winratebalance*100, 2);
			$damagetaken=number_format($db->query("SELECT SUM(damagetaken)/$games FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_one='$weapon' OR weapon_two='$weapon') AND $dayscondition")->fetch_array()[0]);
			$legends=number_format($db->query("SELECT COUNT(legend_id) FROM legends WHERE weapon_one='$weapon' OR weapon_two='$weapon'")->fetch_array()[0]);
			?>
			<div class="card" id="<?=legendName2divId($weapon)?>">
				<img alt="Weapon image" src="/img/weapons/<?=$weapon?>.png" style="border-radius:0"/>
				<p><a href="#<?=legendName2divId($weapon)?>"><b><?=weaponId2Name($weapon)?></b></a>
				<i class="fa fa-chevron-up active orderfactor" data-name="name" data-value="<?=legendName2divId($weapon)?>"></i>
				</p>
				<div class="statistical">
					<div><p>Playrate <i class="fa fa-chevron-down orderfactor" data-name="playrate" data-value="<?=$playrate?>"></i></p> <?=$playrate?>%</div>
					<div><p>Winrate <i class="fa fa-chevron-down orderfactor" data-name="winrate" data-value="<?=$winrate?>"></i></p> <?=$winrate?>%</div>
					<div><p>Dmg taken <i class="fa fa-chevron-down orderfactor" data-name="damagetaken" data-value="<?=$damagetaken?>"></i></p> <?=$damagetaken?></div>
					<div><p>Legends <i class="fa fa-chevron-down orderfactor" data-name="legends" data-value="<?=$legends?>"></i></p> <?=$legends?></div>
					<div><p>Dmg dealt <i class="fa fa-chevron-down orderfactor" data-name="damagedealt" data-value="<?=$damagedealt?>"></i></p> <?=number_format($damagedealt)?></div>
					<div><p>Match duration <i class="fa fa-chevron-down orderfactor" data-name="matchtime" data-value="<?=number_format($matchtime)?>"></i></p> <?
					echo number_format($matchtime);
					?> seconds</div>
				</div>
			</div>
			<?
		}
		?>
		</div>
<?
include('footer.php');
?>