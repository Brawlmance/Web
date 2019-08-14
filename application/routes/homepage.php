		<h1>Most played legends</h1>
		<div class="grid">
		<?
		$legends=$db->query("SELECT * FROM legends ORDER BY (SELECT SUM(games) FROM stats WHERE legend_id=legends.legend_id AND $dayscondition) DESC LIMIT 4");
		while($legend=$legends->fetch_array()) {
            $legendStats = getStats($legend['legend_id'], $patchid, $tier);
            if (!$legendStats) continue;

			?><div class="card" id="<?=legendName2divId($legend['bio_name'])?>">
				<img alt="Legend image" src="/img/legends/<?=$legend['legend_id']?>.png" />
				<p><a href="/legends<?=$linksquery?>#<?=legendName2divId($legend['bio_name'])?>"><b><?=$legend['bio_name']?></b></a>
				<i class="fa fa-chevron-down orderfactor" data-name="name" data-value="<?=legendName2divId($legend['bio_name'])?>"></i>
				</p>
				<div class="stats">
					<div class="strength"><?=$legend['strength']?></div>
					<div class="dexterity"><?=$legend['dexterity']?></div>
					<div class="defense"><?=$legend['defense']?></div>
					<div class="speed"><?=$legend['speed']?></div>
				</div>
				<div class="statistical">
					<div><p>Playrate <i class="fa fa-chevron-down active orderfactor" data-name="playrate" data-value="<?=$legendStats['playrate']?>"></i></p> <?=$legendStats['playrate']?>%</div>
					<div><p>Winrate <i class="fa fa-chevron-down orderfactor" data-name="winrate" data-value="<?=$legendStats['winrate']?>"></i></p> <?=$legendStats['winrate']?>%</div>
					<div><p>Dmg taken <i class="fa fa-chevron-down orderfactor" data-name="damagetaken" data-value="<?=$legendStats['damagetaken']?>"></i></p> <?=$legendStats['damagetaken']?></div>
					<div><p>Suicides <i class="fa fa-chevron-down orderfactor" data-name="suicides" data-value="<?=$legendStats['suicides']?>"></i></p> <?=$legendStats['suicides']?></div>
					<div><p>Dmg dealt <i class="fa fa-chevron-down orderfactor" data-name="damagedealt" data-value="<?=$legendStats['damagedealt']?>"></i></p> <?=number_format($legendStats['damagedealt'])?>
					<div class="damagedealt">Unarmed: <?
    	            echo number_format($legendStats['damagedealt_unarmed'] / $legendStats['damagedealt'] * 100, 1) . '%';
					?></div>
					<div class="damagedealt">Gadgets: <?
    	            echo number_format($legendStats['damagedealt_gadgets'] / $legendStats['damagedealt'] * 100, 1) . '%';
					?></div>
					<div class="damagedealt"><?=weaponId2Name($legend['weapon_one'])?>: <?
    	            echo number_format($legendStats['damagedealt_weaponone'] / $legendStats['damagedealt'] * 100, 1) . '%';
					?></div>
					<div class="damagedealt"><?=weaponId2Name($legend['weapon_two'])?>: <?
    	            echo number_format($legendStats['damagedealt_weapontwo'] / $legendStats['damagedealt'] * 100, 1) . '%';
					?></div></div>
					<div><p>Match duration <i class="fa fa-chevron-down orderfactor" data-name="matchtime" data-value="<?=number_format($legendStats['matchtime'])?>"></i></p> <?
					echo number_format($legendStats['matchtime']);
					?> seconds
					<div class="matchtime">Unarmed: <?
    	            echo number_format($legendStats['matchtime_unarmed'] / $legendStats['matchtime'] * 100, 1) . '%';
					?></div>
					<div class="matchtime"><?=weaponId2Name($legend['weapon_one'])?>: <?
    	            echo number_format($legendStats['matchtime_weaponone'] / $legendStats['matchtime'] * 100, 1) . '%';
					?></div>
					<div class="matchtime"><?=weaponId2Name($legend['weapon_two'])?>: <?
    	            echo number_format($legendStats['matchtime_weapontwo'] / $legendStats['matchtime'] * 100, 1) . '%';
					?></div></div>
				</div>
			</div><?
		}
		?>
		</div>
		<h1>Highest winrate legends</h1>
		<div class="grid">
		<?
		$legends=$db->query("SELECT * FROM legends ORDER BY (SELECT SUM(wins)/SUM(games) FROM stats WHERE legend_id=legends.legend_id AND $dayscondition) DESC LIMIT 4");
		while($legend=$legends->fetch_array()) {
            $legendStats = getStats($legend['legend_id'], $patchid, $tier);
            if (!$legendStats) continue;

			?><div class="card" id="<?=legendName2divId($legend['bio_name'])?>">
				<img alt="Legend image" src="/img/legends/<?=$legend['legend_id']?>.png" />
				<p><a href="/legends<?=$linksquery?>#<?=legendName2divId($legend['bio_name'])?>"><b><?=$legend['bio_name']?></b></a>
				<i class="fa fa-chevron-down orderfactor" data-name="name" data-value="<?=legendName2divId($legend['bio_name'])?>"></i>
				</p>
				<div class="stats">
					<div class="strength"><?=$legend['strength']?></div>
					<div class="dexterity"><?=$legend['dexterity']?></div>
					<div class="defense"><?=$legend['defense']?></div>
					<div class="speed"><?=$legend['speed']?></div>
				</div>
				<div class="statistical">
					<div><p>Playrate <i class="fa fa-chevron-down orderfactor" data-name="playrate" data-value="<?=$legendStats['playrate']?>"></i></p> <?=$legendStats['playrate']?>%</div>
					<div><p>Winrate <i class="fa fa-chevron-down active orderfactor" data-name="winrate" data-value="<?=$legendStats['winrate']?>"></i></p> <?=$legendStats['winrate']?>%</div>
					<div><p>Dmg taken <i class="fa fa-chevron-down orderfactor" data-name="damagetaken" data-value="<?=$legendStats['damagetaken']?>"></i></p> <?=$legendStats['damagetaken']?></div>
					<div><p>Suicides <i class="fa fa-chevron-down orderfactor" data-name="suicides" data-value="<?=$legendStats['suicides']?>"></i></p> <?=$legendStats['suicides']?></div>
					<div><p>Dmg dealt <i class="fa fa-chevron-down orderfactor" data-name="damagedealt" data-value="<?=$legendStats['damagedealt']?>"></i></p> <?=number_format($legendStats['damagedealt'])?>
					<div class="damagedealt">Unarmed: <?
    	            echo number_format($legendStats['damagedealt_unarmed'] / $legendStats['damagedealt'] * 100, 1) . '%';
					?></div>
					<div class="damagedealt">Gadgets: <?
    	            echo number_format($legendStats['damagedealt_gadgets'] / $legendStats['damagedealt'] * 100, 1) . '%';
					?></div>
					<div class="damagedealt"><?=weaponId2Name($legend['weapon_one'])?>: <?
    	            echo number_format($legendStats['damagedealt_weaponone'] / $legendStats['damagedealt'] * 100, 1) . '%';
					?></div>
					<div class="damagedealt"><?=weaponId2Name($legend['weapon_two'])?>: <?
    	            echo number_format($legendStats['damagedealt_weapontwo'] / $legendStats['damagedealt'] * 100, 1) . '%';
					?></div></div>
					<div><p>Match duration <i class="fa fa-chevron-down orderfactor" data-name="matchtime" data-value="<?=number_format($legendStats['matchtime'])?>"></i></p> <?
					echo number_format($legendStats['matchtime']);
					?> seconds
					<div class="matchtime">Unarmed: <?
    	            echo number_format($legendStats['matchtime_unarmed'] / $legendStats['matchtime'] * 100, 1) . '%';
					?></div>
					<div class="matchtime"><?=weaponId2Name($legend['weapon_one'])?>: <?
    	            echo number_format($legendStats['matchtime_weaponone'] / $legendStats['matchtime'] * 100, 1) . '%';
					?></div>
					<div class="matchtime"><?=weaponId2Name($legend['weapon_two'])?>: <?
    	            echo number_format($legendStats['matchtime_weapontwo'] / $legendStats['matchtime'] * 100, 1) . '%';
					?></div></div>
				</div>
			</div><?
		}
		?>
		</div>
		<h1>Most OP legends</h1>
		<p style="text-align:center">The ones that match your play style and make you happy</p>
		<div class="streams">
		<h1>Top live streams</h1>
		</div>