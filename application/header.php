<?php
include('config.php');
$v=10;
?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Brawlmance - Brawlhalla Statistics</title>
	<meta name="description" content="Brawlmance provides Brawlhalla Statistics for legend winrates, weapon winrates, leaderboards, and more">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="apple-touch-icon" sizes="57x57" href="/img/favicons/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/img/favicons/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/img/favicons/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/img/favicons/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/img/favicons/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/img/favicons/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/img/favicons/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/img/favicons/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/img/favicons/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/img/favicons/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16x16.png">
	<link rel="manifest" href="/manifest.json?v2">
	<meta name="theme-color" content="#FD9700">
	<?php
		includeStylesheet('normalize');
		includeStylesheet('main');
	?>
	<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
</head>
<body>
  <div class="container" id="main">
	<header>
      <div id="menu">
		<ul>
			<li id="brawlmance"><a href="/<?=$linksquery?>"><img src="/img/logo.png" alt="Logo"/> BRAWLMANCE</a></li>
			<li><a href="/legends<?=$linksquery?>">LEGENDS</a></li>
			<li><a href="/weapons<?=$linksquery?>">WEAPONS</a></li>
			<li><a href="/rankings<?=$linksquery?>">RANKINGS</a></li>
			<li><a href="/about<?=$linksquery?>">ABOUT</a></li>
		</ul>
	  </div>
      <div id="aggregationstatus">
		<form method="GET" style="display:inline" id="patchform">
		<label>Patch <select name="patch" onchange="$('#patchform').submit()">
		<?
		$patches=$db->query("SELECT id FROM patches WHERE changes='1' ORDER BY timestamp DESC LIMIT 20");
		while($patch=$patches->fetch_array(true)) {
			echo "<option ",($patch['id']==$patchid ? 'selected' : ''),">$patch[id]</option>";
		}
		?>
		</select></label>
		<input type="hidden" name="tier" value="<?=$tier?>">
		</form>
		<form method="GET" style="display:inline" id="tierform">
		<input type="hidden" name="patch" value="<?=$patchid?>">
		<label><select name="tier" onchange="$('#tierform').submit()">
		<?
		foreach($tiers as $tiername) {
			echo "<option ",($tiername==$tier ? 'selected' : ''),">$tiername</option>";
		}
		?>
		</select></label>
		</form>
		<span id="n_analyzed">Games analyzed: <?=number_format($totalgames)?></span>
	  </div>
	</header>
	<?
	if($totalgames<200000) {
		?>
		  <div id="aggregating_warning">
		   WARNING: We don't have enough data yet
		  </div>
		<?
	}
	?>
	<div id="content">