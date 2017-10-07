<?php
include('header.php');

$path = explode('/', explode('?', ltrim(trim($_SERVER['REQUEST_URI']), '/'))[0]);
switch ($path[0]) {
    case '':
        include('routes/homepage.php');
        break;
    case 'about':
        include('routes/about.php');
        break;
    case 'legends':
        include('routes/legends.php');
        break;
    case 'weapons':
        include('routes/weapons.php');
        break;
    case 'rankings':
        include('routes/rankings.php');
        break;
    case 'search':
        include('routes/search.php');
        break;
    default:
        include('routes/error404.php');
}

include('footer.php');
