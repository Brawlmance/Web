<?
include('header.php');
?>
		<script>
		var startsortfn=function() {
			var orderfactor=Cookies.get('orderfactor'); if(orderfactor==null) orderfactor='name';
			var order=Cookies.get('order'); if(order==null) order='asc';
			tinysort('.card',{selector:'i[data-name="'+orderfactor+'"]',attr: 'data-value', order: order});
			if(order=='asc') {
			  $('.card i[data-name="'+orderfactor+'"]').removeClass('fa-chevron-down').addClass('fa-chevron-up active');
			} else {
			  $('.card i[data-name="'+orderfactor+'"]').addClass('active');
			}
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
			if($games>0) {
				$damageweaponone=$db->query("SELECT SUM(damageweaponone)/$games FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_one='$weapon') AND $dayscondition")->fetch_array()[0];
				$damageweapontwo=$db->query("SELECT SUM(damageweapontwo)/$games FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_two='$weapon') AND $dayscondition")->fetch_array()[0];
				$damagedealt=$damageweaponone+$damageweapontwo;
				$matchtime=$db->query("SELECT SUM(matchtime)/$games FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_one='$weapon' OR weapon_two='$weapon') AND $dayscondition")->fetch_array()[0];
				
				$legends=number_format($db->query("SELECT COUNT(legend_id) FROM legends WHERE weapon_one='$weapon' OR weapon_two='$weapon'")->fetch_array()[0]);
				$playrate=number_format($games/$legends/$totalgames*100, 2);
				$winrate=number_format($db->query("SELECT SUM(wins)/$games FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_one='$weapon' OR weapon_two='$weapon') AND $dayscondition")->fetch_array()[0]*$winratebalance*100, 2);
				$timeheld=number_format($db->query("SELECT SUM(damagetaken)/$games FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_one='$weapon' OR weapon_two='$weapon') AND $dayscondition")->fetch_array()[0]/2);
				$timeheld1=$db->query("SELECT SUM(timeheldweaponone)/$games FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_one='$weapon') AND $dayscondition")->fetch_array()[0];
				$timeheld2=$db->query("SELECT SUM(timeheldweapontwo)/$games FROM stats WHERE legend_id IN (SELECT legend_id FROM legends WHERE weapon_two='$weapon') AND $dayscondition")->fetch_array()[0];
				$timeheld=$timeheld1+$timeheld2;
				?><div class="card" id="<?=legendName2divId($weapon)?>">
					<img alt="Weapon image" src="/img/weapons/<?=$weapon?>.png" style="border-radius:0"/>
					<p><a href="#<?=legendName2divId($weapon)?>"><b><?=weaponId2Name($weapon)?></b></a>
					<i class="fa fa-chevron-up active orderfactor" data-name="name" data-value="<?=legendName2divId($weapon)?>"></i>
					</p>
					<div class="statistical">
						<div><p>Playr/Legends <i class="fa fa-chevron-down orderfactor" data-name="playrate" data-value="<?=$playrate?>"></i></p> <?=$playrate?>%</div>
						<div><p>Winrate <i class="fa fa-chevron-down orderfactor" data-name="winrate" data-value="<?=$winrate?>"></i></p> <?=$winrate?>%</div>
						<div><p>Legends <i class="fa fa-chevron-down orderfactor" data-name="legends" data-value="<?=$legends?>"></i></p> <?=$legends?></div>
						<div><p>Dmg dealt <i class="fa fa-chevron-down orderfactor" data-name="damagedealt" data-value="<?=$damagedealt?>"></i></p> <?=number_format($damagedealt)?></div>
						<div><p>Match duration <i class="fa fa-chevron-down orderfactor" data-name="matchtime" data-value="<?=number_format($matchtime)?>"></i></p> <?=number_format($matchtime)?> seconds</div>
						<div><p>Time Held <i class="fa fa-chevron-down orderfactor" data-name="damagetaken" data-value="<?=number_format($timeheld)?>"></i></p> <?=number_format($timeheld)?> seconds</div>
					</div>
				</div><?
			}
		}
		?>
		</div>
<?
include('footer.php');
?>