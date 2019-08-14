<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css" />
<link rel="stylesheet" href="/css/legend_stats.css?v=<?=$v?>">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
<script src="/js/legend_stats.js?v=<?=$v?>"></script>

<?php
function getRanks($legendID, $patchid, $tier) {
    $legendStats = getStats($legendID, $patchid, $tier);
    $legendData = getLegendData($legendID);
    $statsForAllLegends = getAllLegendStats($patchid, $tier);
    $result = [];
    foreach($legendStats as $key => $myStat) {
        $rank = 1;
        $total = 0;
        foreach($statsForAllLegends as $otherLegend) {
            if ($key === 'damagedealt_weaponone' || $key === 'matchtime_weaponone' || $key === 'damagedealt_weapontwo' || $key === 'matchtime_weapontwo') {
                $thisLegendWeaponKey = strpos($key, 'weaponone') !== false ? 'weapon_one' : 'weapon_two';
                $otherLegendWeaponKey = false;
                $keyInitialPart = explode('_', $key)[0];

                if ($legendData[$thisLegendWeaponKey] === $otherLegend['data']['weapon_one']) {
                    if ($thisLegendWeaponKey === 'weapon_one') $otherLegendWeaponKey = $key;
                    else $otherLegendWeaponKey = $keyInitialPart . '_weapontwo';
                } else if ($legendData[$thisLegendWeaponKey] === $otherLegend['data']['weapon_two']) {
                    if ($thisLegendWeaponKey === 'weapon_two') $otherLegendWeaponKey = $key;
                    else $otherLegendWeaponKey = $keyInitialPart . '_weaponone';
                }
                if ($otherLegendWeaponKey) {
                    $total++;
                    if ($otherLegend['stats'][$otherLegendWeaponKey] > $myStat) $rank++;
                }
                continue;
            }
            $total++;
            if ($otherLegend['stats'][$key] > $myStat) $rank++;
        }
        $result[$key] = [
            'rank' => $rank,
            'total' => $total,
        ];
    }
    return $result;
}

$legendName = str_replace('%20', ' ', $path[1]);
$legendNameEscaped = $db->real_escape_string($legendName);

$legends = $db->query("SELECT legend_id FROM legends WHERE legend_name_key='$legendNameEscaped'")->fetch_assoc();
if(isset($legends['legend_id'])) $legend = getLegendData($legends['legend_id']);

$averagestats = $db->query("SELECT AVG(strength) as strength, AVG(dexterity) as dexterity, AVG(defense) as defense, AVG(speed) as speed FROM legends LIMIT 1")->fetch_assoc();

if ($legend) $legendStats = getStats($legend['legend_id'], $patchid, $tier);

function displayRankDiff($new, $old) {
    $diff = $new - $old;
    if ($diff === 0 || !isset($old)) {
        echo '-';
        return;
    }
    echo ($diff > 0 ? '<span class="change-arrow red">▼</span>' : '<span class="change-arrow green">▲</span>') . abs($diff);
}
function statsMapFn($statKey, $allPatchStats) {
    $result = [];
    foreach($allPatchStats as $patchStats) {
        $formatted = number_format($patchStats[$statKey], 2);
        $result[] = "'$formatted'";
    }
    return $result;
}
function averageStatsMapFn($statKey, $allPatchStats) {
    $result = [];
    foreach($allPatchStats as $patchAllLegendStats) {
        if (sizeof($patchAllLegendStats) === 0) {
            $result[] = "'0'";
            continue;
        }
        $total = 0;
        foreach($patchAllLegendStats as $otherLegend) {
            $total += $otherLegend['stats'][$statKey];
        }
        $total /= sizeof($patchAllLegendStats);
        $total = number_format($total, 2);
        $result[] = "'$total'";
    }
    return $result;
}

if ($legend && $legendStats) {
    $patchesHistory = [];
    $patchesStats = [];
    $patchesAllLegendStats = [];
    $MAX_PATCHES_HISTORY_LENGTH = 10;
    while (sizeof($patchesHistory) < $MAX_PATCHES_HISTORY_LENGTH) {
        $newPatchID = sizeof($patchesHistory) > 0 ? getPreviousPatchID($patchesHistory[0]) : $patchid;
        if (!$newPatchID) break;
        $newStats = getStats($legend['legend_id'], $newPatchID, $tier);
        if (!$newStats) break;
        $newAllLegendStats = getAllLegendStats($newPatchID, $tier);
        if (!$newAllLegendStats) break;
        array_unshift($patchesHistory, $newPatchID);
        array_unshift($patchesStats, $newStats);
        array_unshift($patchesAllLegendStats, $newAllLegendStats);
    }

    $convert_to_string_fn = function($val) {
        return "'$val'";
    };

    $legendRanks = getRanks($legend['legend_id'], $patchid, $tier);
    $previousPatchID = getPreviousPatchID($patchid);
    if ($previousPatchID) {
        $legendLastRanks = getRanks($legend['legend_id'], $previousPatchID, $tier);
    }
    ?>
    <div class="legend-container">
        <div class="legend-column">
        	<a href=""><img class="legend-avatar" alt="Legend image" src="/img/legends/<?=$legend['legend_id']?>.png" /></a>
        	<p class="legend-name"><a href=""><b><?=$legend['bio_name']?></b></a></p>
    	    <h2>Legend Stats Matrix</h2>
    		<canvas id="legend-stats-matrix-canvas"></canvas>
        	<script>
        	generateRadarChart(document.getElementById('legend-stats-matrix-canvas'), {
        		labels: ['Strength', 'Dexterity', 'Defense', 'Speed'],
        		datasets: [{
        			label: 'Legend Stats',
        			backgroundColor: 'rgba(255, 159, 64, 0.4)',
        			borderColor: 'rgb(255, 159, 64)',
        			pointBackgroundColor: 'rgb(255, 159, 64)',
        			data: [
        				<?=$legend['strength']?>,
        				<?=$legend['dexterity']?>,
        				<?=$legend['defense']?>,
        				<?=$legend['speed']?>
        			]
        		}, {
        			label: 'Average',
        			backgroundColor: 'rgba(201, 203, 207, 0.4)',
        			borderColor: 'rgb(201, 203, 207)',
        			pointBackgroundColor: 'rgb(201, 203, 207)',
        			data: [
        				<?=$averagestats['strength']?>,
        				<?=$averagestats['dexterity']?>,
        				<?=$averagestats['defense']?>,
        				<?=$averagestats['speed']?>
        			]
        		}]
        	});
            </script>
        </div>
    	<div class="legend-column">
    	    <h2>Statistics</h2>
    	    <table class="legend-stats-table">
    	        <tr>
    	            <th>Type</th>
    	            <th>Average</th>
    	            <th>Rank</th>
    	            <th>Rank Change this patch</th>
    	        </tr>
    	        <tr>
    	            <td>Playrate</td>
    	            <td><?=$legendStats['playrate']?>%</td>
    	            <td><?=$legendRanks['playrate']['rank']?>/<?=$legendRanks['playrate']['total']?></td>
    	            <td><?displayRankDiff($legendRanks['playrate']['rank'], $legendLastRanks['playrate']['rank'])?></td>
    	        </tr>
    	        <tr>
    	            <td>Winrate</td>
    	            <td><?=$legendStats['winrate']?>%</td>
    	            <td><?=$legendRanks['winrate']['rank']?>/<?=$legendRanks['winrate']['total']?></td>
    	            <td><?displayRankDiff($legendRanks['winrate']['rank'], $legendLastRanks['winrate']['rank'])?></td>
    	        </tr>
    	        <tr>
    	            <td>Damage taken</td>
    	            <td><?=number_format($legendStats['damagetaken'])?></td>
    	            <td><?=$legendRanks['damagetaken']['rank']?>/<?=$legendRanks['damagetaken']['total']?></td>
    	            <td><?displayRankDiff($legendRanks['damagetaken']['rank'], $legendLastRanks['damagetaken']['rank'])?></td>
    	        </tr>
    	        <tr>
    	            <td>Damage dealt</td>
    	            <td><?=number_format($legendStats['damagedealt'])?></td>
    	            <td><?=$legendRanks['damagedealt']['rank']?>/<?=$legendRanks['damagedealt']['total']?></td>
    	            <td><?displayRankDiff($legendRanks['damagedealt']['rank'], $legendLastRanks['damagedealt']['rank'])?></td>
    	        </tr>
    	        <tr class="minor">
    	            <td>(Unarmed)</td>
    	            <td><?php
    	            echo number_format($legendStats['damagedealt_unarmed'] / $legendStats['damagedealt'] * 100, 1) . '%';
    	            ?></td>
    	            <td><?=$legendRanks['damagedealt_unarmed']['rank']?>/<?=$legendRanks['damagedealt_unarmed']['total']?></td>
    	            <td><?displayRankDiff($legendRanks['damagedealt_unarmed']['rank'], $legendLastRanks['damagedealt_unarmed']['rank'])?></td>
    	        </tr>
    	        <tr class="minor">
    	            <td>(Gadgets)</td>
    	            <td><?php
    	            echo number_format($legendStats['damagedealt_gadgets'] / $legendStats['damagedealt'] * 100, 1) . '%';
    	            ?></td>
    	            <td><?=$legendRanks['damagedealt_gadgets']['rank']?>/<?=$legendRanks['damagedealt_gadgets']['total']?></td>
    	            <td><?displayRankDiff($legendRanks['damagedealt_gadgets']['rank'], $legendLastRanks['damagedealt_gadgets']['rank'])?></td>
    	        </tr>
    	        <tr class="minor">
    	            <td>(<?=weaponId2Name($legend['weapon_one'])?>)</td>
    	            <td><?php
    	            echo number_format($legendStats['damagedealt_weaponone'] / $legendStats['damagedealt'] * 100, 1) . '%';
    	            ?></td>
    	            <td><?=$legendRanks['damagedealt_weaponone']['rank']?>/<?=$legendRanks['damagedealt_weaponone']['total']?></td>
    	            <td><?displayRankDiff($legendRanks['damagedealt_weaponone']['rank'], $legendLastRanks['damagedealt_weaponone']['rank'])?></td>
    	        </tr>
    	        <tr class="minor">
    	            <td>(<?=weaponId2Name($legend['weapon_two'])?>)</td>
    	            <td><?php
    	            echo number_format($legendStats['damagedealt_weapontwo'] / $legendStats['damagedealt'] * 100, 1) . '%';
    	            ?></td>
    	            <td><?=$legendRanks['damagedealt_weapontwo']['rank']?>/<?=$legendRanks['damagedealt_weapontwo']['total']?></td>
    	            <td><?displayRankDiff($legendRanks['damagedealt_weapontwo']['rank'], $legendLastRanks['damagedealt_weapontwo']['rank'])?></td>
    	        </tr>
    	        <tr>
    	            <td>Match duration</td>
    	            <td><?=number_format($legendStats['matchtime'])?></td>
    	            <td><?=$legendRanks['matchtime']['rank']?>/<?=$legendRanks['matchtime']['total']?></td>
    	            <td><?displayRankDiff($legendRanks['matchtime']['rank'], $legendLastRanks['matchtime']['rank'])?></td>
    	        </tr>
    	        <tr class="minor">
    	            <td>(Unarmed)</td>
    	            <td><?php
    	            echo number_format($legendStats['matchtime_unarmed'] / $legendStats['matchtime'] * 100, 1) . '%';
    	            ?></td>
    	            <td><?=$legendRanks['matchtime_unarmed']['rank']?>/<?=$legendRanks['matchtime_unarmed']['total']?></td>
    	            <td><?displayRankDiff($legendRanks['matchtime_unarmed']['rank'], $legendLastRanks['matchtime_unarmed']['rank'])?></td>
    	        </tr>
    	        <tr class="minor">
    	            <td>(<?=weaponId2Name($legend['weapon_one'])?>)</td>
    	            <td><?php
    	            echo number_format($legendStats['matchtime_weaponone'] / $legendStats['matchtime'] * 100, 1) . '%';
    	            ?></td>
    	            <td><?=$legendRanks['matchtime_weaponone']['rank']?>/<?=$legendRanks['matchtime_weaponone']['total']?></td>
    	            <td><?displayRankDiff($legendRanks['matchtime_weaponone']['rank'], $legendLastRanks['matchtime_weaponone']['rank'])?></td>
    	        </tr>
    	        <tr class="minor">
    	            <td>(<?=weaponId2Name($legend['weapon_two'])?>)</td>
    	            <td><?php
    	            echo number_format($legendStats['matchtime_weapontwo'] / $legendStats['matchtime'] * 100, 1) . '%';
    	            ?></td>
    	            <td><?=$legendRanks['matchtime_weapontwo']['rank']?>/<?=$legendRanks['matchtime_weapontwo']['total']?></td>
    	            <td><?displayRankDiff($legendRanks['matchtime_weapontwo']['rank'], $legendLastRanks['matchtime_weapontwo']['rank'])?></td>
    	        </tr>
    	    </table>
    	</div>
    	<div class="legend-column">
    	    <h2>Win Rate % By Patch</h2>
    		<canvas id="legend-stats-win-rate"></canvas>
        	<script>
        	generatePatchChart(document.getElementById('legend-stats-win-rate'), {
        		labels: [<?=join(', ', array_map($convert_to_string_fn, $patchesHistory))?>],
        		datasets: [{
        			label: 'Legend',
        			backgroundColor: 'rgba(255, 159, 64, 0.4)',
        			borderColor: 'rgb(255, 159, 64)',
        			pointBackgroundColor: 'rgb(255, 159, 64)',
        			data: [<?=join(', ', statsMapFn('winrate', $patchesStats))?>]
        		}, {
        			label: 'Average',
        			backgroundColor: 'rgba(201, 203, 207, 0.4)',
        			borderColor: 'rgb(201, 203, 207)',
        			pointBackgroundColor: 'rgb(201, 203, 207)',
        			data: [<?=join(', ', averageStatsMapFn('winrate', $patchesAllLegendStats))?>]
        		}]
        	});
            </script>
    	    <h2>Play Rate % By Patch</h2>
    		<canvas id="legend-stats-play-rate"></canvas>
        	<script>
        	generatePatchChart(document.getElementById('legend-stats-play-rate'), {
        		labels: [<?=join(', ', array_map($convert_to_string_fn, $patchesHistory))?>],
        		datasets: [{
        			label: 'Legend',
        			backgroundColor: 'rgba(255, 159, 64, 0.4)',
        			borderColor: 'rgb(255, 159, 64)',
        			pointBackgroundColor: 'rgb(255, 159, 64)',
        			data: [<?=join(', ', statsMapFn('playrate', $patchesStats))?>]
        		}, {
        			label: 'Average',
        			backgroundColor: 'rgba(201, 203, 207, 0.4)',
        			borderColor: 'rgb(201, 203, 207)',
        			pointBackgroundColor: 'rgb(201, 203, 207)',
        			data: [<?=join(', ', averageStatsMapFn('playrate', $patchesAllLegendStats))?>]
        		}]
        	});
            </script>
    	</div>
    </div>
    <?
} else {
    echo 'Unknown legend';
}
