<?
include('header.php');
?>
		<div class="tops">
			<div class="winrate">
				<h1>Winrates</h1>
				<table>
				<tr>
					<th>Role</th>
					<th>Highest</th>
					<th>Lowest</th>
				</tr>
				<?
				$roles=$db->query("SELECT distinct role as role FROM legends WHERE role IS NOT NULL ORDER BY role");
				while($role=$roles->fetch_array()) {
					$role=$role['role'];
					$highest=$db->query("SELECT legend_id, SUM(wins)/SUM(games)*100 as winrate, (SELECT bio_name FROM legends WHERE legend_id=stats.legend_id) as bio_name FROM stats WHERE legend_id in (select legend_id from legends  where role='$role') AND $dayscondition  group by legend_id order by winrate desc limit 1");
					$lowest=$db->query("SELECT legend_id, SUM(wins)/SUM(games)*100 as winrate, (SELECT bio_name FROM legends WHERE legend_id=stats.legend_id) as bio_name FROM stats WHERE legend_id in (select legend_id from legends  where role='$role') AND $dayscondition group by legend_id order by winrate asc limit 1");
					if($highest->num_rows>0 && $lowest->num_rows>0) {
						$highest=$highest->fetch_array();
						$lowest=$lowest->fetch_array();
						?>
						<tr>
							<td class="rolename"><?=$rolenames[$role]?></td>
							<td class="highest"><a href="#<?=legendName2divId($highest['bio_name'])?>"><?=$highest['bio_name']?></a> <?=number_format($highest['winrate']*$winratebalance, 2)?>%</td>
							<td class="lowest"><a href="#<?=legendName2divId($lowest['bio_name'])?>"><?=$lowest['bio_name']?></a> <?=number_format($lowest['winrate']*$winratebalance, 2)?>%</td>
						</tr>
						<?
					}
				}
				?>
				</table>
			</div>
			<div class="playrate">
				<h1>Playrates</h1>
				<table>
				<tr>
					<th>Role</th>
					<th>Highest</th>
					<th>Lowest</th>
				</tr>
				<?
				$roles=$db->query("SELECT distinct role as role FROM legends WHERE role IS NOT NULL ORDER BY role");
				while($role=$roles->fetch_array()) {
					$role=$role['role'];
					$highest=$db->query("SELECT legend_id, SUM(games)/$totalgames*100 as playrate, (SELECT bio_name FROM legends WHERE legend_id=stats.legend_id) as bio_name FROM stats WHERE legend_id in (select legend_id from legends where role='$role') AND $dayscondition group by legend_id order by playrate desc limit 1");
					$lowest=$db->query("SELECT legend_id, SUM(games)/$totalgames*100 as playrate, (SELECT bio_name FROM legends WHERE legend_id=stats.legend_id) as bio_name FROM stats WHERE legend_id in (select legend_id from legends  where role='$role') AND $dayscondition group by legend_id order by playrate asc limit 1");
					if($highest->num_rows>0 && $lowest->num_rows>0) {
						$highest=$highest->fetch_array();
						$lowest=$lowest->fetch_array();
						?>
						<tr>
							<td class="rolename"><?=$rolenames[$role]?></td>
							<td class="highest"><a href="#<?=legendName2divId($highest['bio_name'])?>"><?=$highest['bio_name']?></a> <?=number_format($highest['playrate'], 2)?>%</td>
							<td class="lowest"><a href="#<?=legendName2divId($lowest['bio_name'])?>"><?=$lowest['bio_name']?></a> <?=number_format($lowest['playrate'], 2)?>%</td>
						</tr>
						<?
					}
				}
				?>
				</table>
			</div>
		</div>
		<div class="grid">
		<?
		$legends=$db->query("SELECT * FROM legends ORDER BY bio_name");
		while($legend=$legends->fetch_array()) {
			$games=$db->query("SELECT SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
			$damagedealt=$db->query("SELECT SUM(damagedealt)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
			$matchtime=$db->query("SELECT SUM(matchtime)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
			$timeheldweaponone=$db->query("SELECT SUM(timeheldweaponone)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
			$timeheldweapontwo=$db->query("SELECT SUM(timeheldweapontwo)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
			?>
			<div class="card" id="<?=legendName2divId($legend['bio_name'])?>">
				<img alt="Legend preview" src="/img/legends/<?=$legend['legend_id']?>.png" />
				<p><a href="#<?=legendName2divId($legend['bio_name'])?>"><b><?=$legend['bio_name']?></b></a>, <i><? if($legend['role']!="") echo $rolenames[$legend['role']]; else echo "New!";?></i></p>
				<div class="stats">
					<div class="strength"><?=$legend['strength']?></div>
					<div class="dexterity"><?=$legend['dexterity']?></div>
					<div class="defense"><?=$legend['defense']?></div>
					<div class="speed"><?=$legend['speed']?></div>
				</div>
				<div class="statistical">
					<div><p>Playrate</p> <?
					echo number_format($games/$totalgames*100, 2);
					?>%</div>
					<div><p>Winrate</p> <?
					echo number_format($db->query("SELECT SUM(wins)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]*$winratebalance*100, 2);
					?>%</div>
					<div><p>Dmg taken</p> <?
					echo number_format($db->query("SELECT SUM(damagetaken)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]);
					?></div>
					<div><p>Suicides</p> <?
					echo number_format($db->query("SELECT SUM(suicides)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0], 2);
					?></div>
					<div><p>Dmg dealt</p> <?
					echo number_format($damagedealt);
					?>
					<div class="damagedealt">Unarmed: <?
					echo number_format($db->query("SELECT SUM(damageunarmed)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]/$damagedealt*100, 1).'%';
					?></div>
					<div class="damagedealt">Gadgets: <?
					echo number_format($db->query("SELECT SUM(damagegadgets)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]/$damagedealt*100, 1).'%';
					?></div>
					<div class="damagedealt"><?=$legend['weapon_one']?>: <?
					echo number_format($db->query("SELECT SUM(damageweaponone)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]/$damagedealt*100, 1).'%';
					?></div>
					<div class="damagedealt"><?=$legend['weapon_two']?>: <?
					echo number_format($db->query("SELECT SUM(damageweapontwo)/$games FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]/$damagedealt*100, 1).'%';
					?></div></div>
					<div><p>Match duration</p> <?
					echo number_format($matchtime);
					?> seconds
					<div class="matchtime">Unarmed: <?
					echo number_format((1-($timeheldweaponone+$timeheldweapontwo)/$matchtime)*100, 1).'%';
					?></div>
					<div class="matchtime"><?=$legend['weapon_one']?>: <?
					echo number_format($timeheldweaponone/$matchtime*100, 1).'%';
					?></div>
					<div class="matchtime"><?=$legend['weapon_two']?>: <?
					echo number_format($timeheldweapontwo/$matchtime*100, 1).'%';
					?></div></div>
				</div>
			</div>
			<?
		}
		?>
		</div>
		<div class="streams">
		<h1>Top live streams</h1>
		</div>
<?
include('footer.php');
?>