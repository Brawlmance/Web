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
		$legends=$db->query("SELECT * FROM legends ORDER BY bio_name");
		while($legend=$legends->fetch_array()) {
			$games=$db->query("SELECT SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
			if($games==0) continue;
			$damagedealt=$db->query("SELECT SUM(damagedealt)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
			if($damagedealt==0) continue;
			$matchtime=$db->query("SELECT SUM(matchtime)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
			if($matchtime==0) continue;
			$timeheldweaponone=$db->query("SELECT SUM(timeheldweaponone)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
			$timeheldweapontwo=$db->query("SELECT SUM(timeheldweapontwo)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
			
			$playrate=number_format($games/$totalgames*100, 2);
			$winrate=number_format($db->query("SELECT SUM(wins)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]*$winratebalance*100, 2);
			$damagetaken=number_format($db->query("SELECT SUM(damagetaken)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]);
			$suicides=number_format($db->query("SELECT SUM(suicides)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0], 2);
			?><div class="card" id="<?=legendName2divId($legend['bio_name'])?>">
				<img alt="Legend image" src="/img/legends/<?=$legend['legend_id']?>.png" />
				<p><a href="#<?=legendName2divId($legend['bio_name'])?>"><b><?=$legend['bio_name']?></b></a>
				<i class="fa fa-chevron-down orderfactor" data-name="name" data-value="<?=legendName2divId($legend['bio_name'])?>"></i>
				</p>
				<div class="stats">
					<div class="strength"><?=$legend['strength']?></div>
					<div class="dexterity"><?=$legend['dexterity']?></div>
					<div class="defense"><?=$legend['defense']?></div>
					<div class="speed"><?=$legend['speed']?></div>
				</div>
				<div class="statistical">
					<div><p>Playrate <i class="fa fa-chevron-down orderfactor" data-name="playrate" data-value="<?=$playrate?>"></i></p> <?=$playrate?>%</div>
					<div><p>Winrate <i class="fa fa-chevron-down orderfactor" data-name="winrate" data-value="<?=$winrate?>"></i></p> <?=$winrate?>%</div>
					<div><p>Dmg taken <i class="fa fa-chevron-down orderfactor" data-name="damagetaken" data-value="<?=$damagetaken?>"></i></p> <?=$damagetaken?></div>
					<div><p>Suicides <i class="fa fa-chevron-down orderfactor" data-name="suicides" data-value="<?=$suicides?>"></i></p> <?=$suicides?></div>
					<div><p>Dmg dealt <i class="fa fa-chevron-down orderfactor" data-name="damagedealt" data-value="<?=$damagedealt?>"></i></p> <?=number_format($damagedealt)?>
					<div class="damagedealt">Unarmed: <?
					echo number_format($db->query("SELECT SUM(damageunarmed)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]/$damagedealt*100, 1).'%';
					?></div>
					<div class="damagedealt">Gadgets: <?
					echo number_format($db->query("SELECT SUM(damagegadgets)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]/$damagedealt*100, 1).'%';
					?></div>
					<div class="damagedealt"><?=weaponId2Name($legend['weapon_one'])?>: <?
					echo number_format($db->query("SELECT SUM(damageweaponone)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]/$damagedealt*100, 1).'%';
					?></div>
					<div class="damagedealt"><?=weaponId2Name($legend['weapon_two'])?>: <?
					echo number_format($db->query("SELECT SUM(damageweapontwo)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]/$damagedealt*100, 1).'%';
					?></div></div>
					<div><p>Match duration <i class="fa fa-chevron-down orderfactor" data-name="matchtime" data-value="<?=number_format($matchtime)?>"></i></p> <?
					echo number_format($matchtime);
					?> seconds
					<div class="matchtime">Unarmed: <?
					echo number_format((1-($timeheldweaponone+$timeheldweapontwo)/$matchtime)*100, 1).'%';
					?></div>
					<div class="matchtime"><?=weaponId2Name($legend['weapon_one'])?>: <?
					echo number_format($timeheldweaponone/$matchtime*100, 1).'%';
					?></div>
					<div class="matchtime"><?=weaponId2Name($legend['weapon_two'])?>: <?
					echo number_format($timeheldweapontwo/$matchtime*100, 1).'%';
					?></div></div>
				</div>
			</div><?
		}
		?>
		</div>