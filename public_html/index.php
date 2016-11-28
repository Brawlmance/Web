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
							<td><?=$rolenames[$role]?></td>
							<td><a href="#<?=$highest['bio_name']?>"><?=$highest['bio_name']?> <?=number_format($highest['winrate']*$winratebalance, 2)?>%</a></td>
							<td><a href="#<?=$lowest['bio_name']?>"><?=$lowest['bio_name']?> <?=number_format($lowest['winrate']*$winratebalance, 2)?>%</a></td>
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
							<td><?=$rolenames[$role]?></td>
							<td><a href="#<?=$highest['bio_name']?>"><?=$highest['bio_name']?> <?=number_format($highest['playrate'], 2)?>%</a></td>
							<td><a href="#<?=$lowest['bio_name']?>"><?=$lowest['bio_name']?> <?=number_format($lowest['playrate'], 2)?>%</a></td>
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
			?>
			<div class="card" id="<?=$legend['bio_name']?>">
				<img src="/img/legends/<?=$legend['legend_id']?>.png" />
				<p><b><?=$legend['bio_name']?></b>, <i><? if($legend['role']!="") echo $rolenames[$legend['role']]; else echo "New!";?></i></p>
				<div class="stats">
					<div class="strength"><?=$legend['strength']?></div>
					<div class="dexterity"><?=$legend['dexterity']?></div>
					<div class="defense"><?=$legend['defense']?></div>
					<div class="speed"><?=$legend['speed']?></div>
				</div>
				<div class="statistical">
					<div>Playrate: <?
					echo number_format($db->query("SELECT SUM(games)/$totalgames FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]*100, 2);
					?>%</div>
					<div>Winrate: <?
					echo number_format($db->query("SELECT SUM(wins)/SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]*$winratebalance*100, 2);
					?>%</div>
					<div>Dmg dealt: <?
					echo number_format($db->query("SELECT SUM(damagedealt)/SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]);
					?></div>
					<div class="damagedealt">Unarmed: <?
					echo number_format($db->query("SELECT SUM(damageunarmed)/SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]);
					?></div>
					<div class="damagedealt">Gadgets: <?
					echo number_format($db->query("SELECT SUM(damagegadgets)/SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]);
					?></div>
					<div class="damagedealt"><?=$legend['weapon_one']?>: <?
					echo number_format($db->query("SELECT SUM(damageweaponone)/SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]);
					?></div>
					<div class="damagedealt"><?=$legend['weapon_two']?>: <?
					echo number_format($db->query("SELECT SUM(damageweapontwo)/SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]);
					?></div>
					<div>Dmg taken: <?
					echo number_format($db->query("SELECT SUM(damagetaken)/SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]);
					?></div>
					<div>Suicides: <?
					echo number_format($db->query("SELECT SUM(suicides)/SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0], 2);
					?></div>
					<div>Match duration (s): <?
					echo number_format($db->query("SELECT SUM(matchtime)/SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]);
					?></div>
					<div class="matchtime">Unarmed: <?
					echo number_format($db->query("SELECT (SUM(matchtime)-SUM(timeheldweaponone)-SUM(timeheldweapontwo))/SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]);
					?></div>
					<div class="matchtime"><?=$legend['weapon_one']?>: <?
					echo number_format($db->query("SELECT SUM(timeheldweaponone)/SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]);
					?></div>
					<div class="matchtime"><?=$legend['weapon_two']?>: <?
					echo number_format($db->query("SELECT SUM(timeheldweapontwo)/SUM(games) FROM stats WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]);
					?></div>
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