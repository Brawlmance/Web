<link rel="stylesheet" href="/css/search.css?v=<?=$v?>">

<?php
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
    $player = $db->query("SELECT *, (SELECT COUNT(*) FROM player_legends WHERE brawlhalla_id=players.brawlhalla_id) as player_legends_count FROM players WHERE brawlhalla_id='$brawlhalla_id'")->fetch_array(true);
    if ($player && $player['player_legends_count']) {
        $randomlegend = $db->query("SELECT legend_id FROM legends ORDER BY RAND() LIMIT 1")->fetch_array()[0];
        $player_clan = $db->query("SELECT clans.*, clan_members.* FROM clan_members JOIN clans ON clans.clan_id=clan_members.clan_id WHERE clan_members.brawlhalla_id='$brawlhalla_id'");
        if ($player_clan->num_rows>0) {
            $player_clan=$player_clan->fetch_array(true);
        } else {
            $player_clan=false;
        }
        $legends_query = $db->query("SELECT player_legends.*, legends.*, player_ranked_legends.rating, player_ranked_legends.peak_rating FROM player_legends JOIN legends ON legends.legend_id=player_legends.legend_id
                                     LEFT JOIN player_ranked_legends ON player_ranked_legends.brawlhalla_id = player_legends.brawlhalla_id AND player_ranked_legends.legend_id = player_legends.legend_id
                                     WHERE player_legends.brawlhalla_id='$brawlhalla_id' ORDER BY player_legends.wins DESC");
        $legends=array();
        $overall_total_games = 0;
        $overall_damage_dealt = 0;
        $overall_damage_taken = 0;
        while ($legend = $legends_query->fetch_array(true)) {
            $legends[]=$legend;
            $overall_total_games+=$legend['games'];
            $overall_damage_dealt+=$legend['damagedealt'];
            $overall_damage_taken+=$legend['damagetaken'];
        }
    
        ?>
          <div class="profile-header">
            <div class="name">
            <a href='/search?brawlhalla_id=<?=$brawlhalla_id?>'>
              <img class="avatar" alt='Avatar image' src='/img/legends/<?=$legends[0]['legend_id']?>.png' />
            </a>
            <a href='/search?brawlhalla_id=<?=$brawlhalla_id?>'>
              <h1><?=$player['name']?> (<?=$player['region']?>)</h1>
            </a>
            <?
            if ($player_clan) {
            ?><p><?=$player_clan['clan_name']?></p><?
            }
            ?>
            <p>Updated <?=time_elapsed_string('@'.$player['lastupdated'])?></p>
          </div>
          <div class="info">
            <div class="stat">
              <strong><?=$player['tier']?></strong>
              <span>Tier</span>
            </div>
            <div class="stat">
              <strong>#<?=$player['rank']?></strong>
              <span>Ranking</span>
            </div>
            <div class="stat">
              <strong><?=$player['rating']?></strong>
              <span>Elo</span>
            </div>
          </div>
        </div>
    
        <h1>OVERALL SEASON PERFORMANCE</h1>
        <div class="profile-overall-performance">
            <div class="stats">
                <div class="stat">
                    <strong><?=$player['level']?></strong>
                    <span>Level</span>
                </div>
                <div class="stat">
                    <strong><?=$player['wins']?> - <?=$player['games']-$player['wins']?></strong>
                    <span>Win - Loss</span>
                </div>
                <div class="stat">
                    <strong><?=floor($player['wins']/$player['games']*1000)/10?>%</strong>
                    <span>Winrate</span>
                </div>
                <div class="stat">
                    <strong><?=floor($overall_damage_dealt/$overall_total_games*10)/10?></strong>
                    <span>Avg Damage Dealt</span>
                </div>
                <div class="stat">
                    <strong><?=floor($overall_damage_taken/$overall_total_games*10)/10?></strong>
                    <span>Avg Damage Taken</span>
                </div>
            </div>
        </div>
    
        <h1>LEGEND STATS</h1>
        <?
        foreach ($legends as $legend) {
        ?>
        <div class="player-legend">
            <div class="name">
                <img class="legend-image" alt='Legend image' title="<?=$legend['bio_name']?>" src='/img/legends/<?=$legend['legend_id']?>.png' />
            </div>
            <div class="stat">
                <strong><?=$legend['level']?></strong>
                <span>Level</span>
            </div>
            <div class="stat">
                <strong><?=$legend['wins']?> / <?=$legend['games']-$legend['wins']?></strong>
                <span>Win / Loss</span>
            </div>
            <div class="stat">
                <strong><?=$legend['rating']?> / <?=$legend['peak_rating']?></strong>
                <span>Elo / Peak elo</span>
            </div>
            <div class="stat">
                <strong><?=$legend['games'] ? floor($legend['wins']/$legend['games']*1000)/10 : 0?>%</strong>
                <span>Winrate</span>
            </div>
            <div class="stat">
                <strong><?=floor($legend['games']/$overall_total_games*1000)/10?>%</strong>
                <span>Playrate</span>
            </div>
            <div class="stat">
                <strong><?=$legend['games'] ? floor($legend['damagedealt']/$legend['games']*10)/10 : 0?></strong>
                <span>Avg Dmg dealt</span>
            </div>
            <div class="stat">
                <strong><?=$legend['games'] ? floor($legend['damagetaken']/$legend['games']*10)/10 : 0?></strong>
                <span>Avg Dmg taken</span>
            </div>
            <div class="stat">
                <strong><?=$legend['games'] ? floor($legend['matchtime']/$legend['games']) : 0?>s</strong>
                <span>Avg Match duration</span>
            </div>
        </div>
        <?
        }
    } else {
        echo 'This player is not in our database';
    }
}
