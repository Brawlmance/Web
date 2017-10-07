<?php
includeStylesheet('search');


if (empty($_REQUEST['brawlhalla_id'])) {
?>
<form id="searchForm">
<?php
echoHiddenInputsForQueryStrings(array('brawlhalla_id'));
?>
<label>Brawlhalla ID: <input type="number" name="brawlhalla_id"/></label>
<input type="submit" value="Search" />
</form>
<?php
} else {
    $brawlhalla_id = intval($_REQUEST['brawlhalla_id']);
    $player = $db->query("SELECT * FROM players WHERE brawlhalla_id='$brawlhalla_id'")->fetch_array(true);
    if ($player) {
        $randomlegend = $db->query("SELECT legend_id FROM legends ORDER BY RAND() LIMIT 1")->fetch_array()[0];
        $player_clan = $db->query("SELECT clans.*, clan_members.* FROM clan_members JOIN clans ON clans.clan_id=clan_members.clan_id WHERE clan_members.brawlhalla_id='$brawlhalla_id'");
        if ($player_clan->num_rows>0) {
            $player_clan=$player_clan->fetch_array(true);
        } else {
            $player_clan=false;
        }

        ?>
    
      <div class="profile-header">
        <div class="name">
        <a href='/search?brawlhalla_id=<?=$brawlhalla_id?>'>
          <img class="avatar" alt='Avatar image' src='/img/legends/<?=$randomlegend?>.png' />
        </a>
        <a href='/search?brawlhalla_id=<?=$brawlhalla_id?>'>
          <h1><?=$player['name']?> (<?=$player['region']?>)</h1>
        </a>
        <p>Updated <?=time_elapsed_string('@'.$player['lastupdated'])?></p>
      </div>
      <div class="info">
        <div class="stat">
          <strong><?=$player['level']?></strong>
          <span>Level</span>
        </div>
        <div class="stat">
          <strong>#<?=$player['rank']?></strong>
          <span>Ranking</span>
        </div>
        <div class="stat">
          <strong><?=$player['wins']?></strong>
          <span>Wins</span>
        </div>
      </div>
    </div>
    
    
    
        <?php
        if ($player_clan) {
            echo "$player_clan[clan_name]";
            echo "<br/>";
        }
  
        echo "$player[tier]";
        echo "<br/>";
        echo "$player[rating]";
        echo "<br/>";
    
        ?>
    Average for all legends, and then foreach legend:
      <div class="player-legends">
        <?
        $lastday=$db->query("SELECT MAX(day) FROM playerlegends WHERE brawlhalla_id='$brawlhalla_id'")->fetch_array()[0];
        $dayscondition = "brawlhalla_id='$brawlhalla_id' AND day='$lastday'";
        $totalgames=$db->query("SELECT SUM(games) FROM playerlegends WHERE $dayscondition")->fetch_array()[0];
        $totalwins=$db->query("SELECT SUM(wins) FROM playerlegends WHERE $dayscondition")->fetch_array()[0];
        if ($totalwins==0) {
            $winratebalance=1;
        } else {
            $winratebalance=$totalgames/$totalwins/2; // Because we're not counting lower elos, we will have more wins than losses. We use this variable to normalize the winrates
        }
          $legends=$db->query("SELECT * FROM legends ORDER BY bio_name");
        while ($legend=$legends->fetch_array()) {
            $games=$db->query("SELECT SUM(games) from playerlegends WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
            if ($games==0) {
                continue;
            }
            $damagedealt=$db->query("SELECT SUM(damagedealt)/$games from playerlegends WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
            if ($damagedealt==0) {
                continue;
            }
            $matchtime=$db->query("SELECT SUM(matchtime)/$games from playerlegends WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
            if ($matchtime==0) {
                continue;
            }
            $timeheldweaponone=$db->query("SELECT SUM(timeheldweaponone)/$games from playerlegends WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
            $timeheldweapontwo=$db->query("SELECT SUM(timeheldweapontwo)/$games from playerlegends WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0];
        
            $playrate=number_format($games/$totalgames*100, 2);
            $winrate=number_format($db->query("SELECT SUM(wins)/$games from playerlegends WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]*$winratebalance*100, 2);
            $damagetaken=number_format($db->query("SELECT SUM(damagetaken)/$games from playerlegends WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0]);
            $suicides=number_format($db->query("SELECT SUM(suicides)/$games from playerlegends WHERE legend_id=$legend[legend_id] AND $dayscondition")->fetch_array()[0], 2);
            ?>
            <div class="player-legend" id="<?=legendName2divId($legend['bio_name'])?>">
            <img alt="Legend image" src="/img/legends/<?=$legend['legend_id']?>.png" />
            <p><a href="#<?=legendName2divId($legend['bio_name'])?>"><b><?=$legend['bio_name']?></b></a>
          </p>
          Playrate
          Winrate
          level
          damagedealt
          damagetaken
          kos
          falls
          Match duration
        </div>
            <?php
        }
            ?>
        </div>
        <?php
    } else {
        echo 'This player is not in our database';
    }
}
