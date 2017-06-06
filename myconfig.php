<?php

define('__DEVELOPER', true);
define('__WORK', '/secti/dumbomodel');
define('__DAO_DEBUG', true);
define('__OTIMIZE', true);
define("__LOG", '/log/');
define("__NAME", 'local.testes');



static $myconnections = [
    
    'default' => "pgsql:dbname=dumbomodel; user=postgres; password=123456; host=localhost; port=5432"
];

static $myrequires= [
    
    '/util/incall.inc.php','/util/ModeloCombo.class.php'    
];

/*
 * 
  ╔═══════════════════════════════╗
  ╠═════════════════════▄█════════╣
  ╠══════════════════▄██░█════════╣
  ╠═══════════█═════██░░░█════════╣
  ╠══════════██════██░░░░█════════╣
  ╠══════════█░█══▄█░░░░░█════════╣
  ╠═════════██░░█═█░░░░░░█════════╣
  ╠════════▄█░░░███░░░░░██════════╣
  ╠════════█░░░░█░░░░░░██████████▀╣
  ╠════════█░░░░░░░░░░░██░░░░░░██═╣
  ╠════════█░░░░▄▄▄▄▄░░░░░░░░░░█══╣
  ╠════════█░░▄▀░░░░░▀▄░░░░░░░█═══╣
  ╠═▀███████░▐░░░░░░░░░▌░░░░░█════╣
  ╠═══█░░░░░░▌░░░▄▄▄▄▄▄█▄░░░██════╣
  ╠═══██░░░░▐░░▄██████████▄░█═════╣
  ╠════█░░░░▌▄███████▀▀▀▀█▀█▀═════╣
  ╠════██░░▄▄████████░░░░▌░██═════╣
  ╠═════█▐██████▌███▌░░░░▐░░██════╣
  ╠══════██▌████▐██▐░░░░░▐░░░░██══╣
  ╠═════█▐█▐▌█▀███▀▀░░░░░▐░░░░░██═╣
  ╠════█░░██▐█░░▄░░░▄█░░░▐░░░░░░██╣
  ╠═══█░░░▐▌█▌▀▀░░░█░█▌░░▐░░░░████╣
  ╠══█░░░░▐▀▀░░░░▄█░░█▌░░▌░░███═══╣
  ╠▄██░░░░░▌░░░▄██░▄██▌░▐░███═════╣
  ╠═══██░░░▐░░▀█▄░▄███▌░▐░░█══════╣
  ╠════███░░█░░░██▀▀░█░░▌░░░█═════╣
  ╠══════█░░░▌░░▐█████░▐░░░░██════╣
  ╠═════█░░░░▐░░░░▀▀▀░░▌░░░░░█════╣
  ╠═════█░░░░░▀▄▄░░░░░█░░░░░░██═══╣
  ╠════██░░░░░░░░▀▄▄▄▀░░████████▄═╣
  ╠════█░░░░░█░░░░░░░░░░█═════════╣
  ╠════█░█████░░░░░█░░░░█═════════╣
  ╠════██════██░░░░██░░░█═════════╣
  ╠════█══════█░░░█═███░░█════════╣
  ╠═══════════█░░░█═══██░█════════╣
  ╠═══════════█░░░█═════██════════╣
  ╠═══════════██░░█══════█════════╣
  ╠════════════█░░█═══════════════╣
  ╠═════════════█░█═══════════════╣
  ╠══════════════██═══════════════╣
  ╚═══════════════════════════════╝

 */
define("__ROOT", $_SERVER['DOCUMENT_ROOT']);
define("__DIR", __ROOT . __WORK . "/");
define("DIR_LOG", _file(__LOG));
require_once (_file('/lib/myphp/log.inc.php'));
require_once (_file('/lib/myphp/Accessors.php'));
require_once (_file('/lib/myphp/Singleton.class.php'));
require_once (_file('/lib/myphp/DAO.class.php'));
require_once (_file('/lib/myphp/Action.inc.php'));
require_once (_file('/lib/myphp/autoload.php'));
require_once (_file('/controllers/Controllers.php'));
foreach($myrequires as $r){
    require_once (_file($r));
}



function _src($src) {


    if ($src[0] == '/') {
        $src = __WORK . $src;
    }

    return $src;
}

function _url($url) {


    if ($url[0] == '/') {

        $url = "http://" . $_SERVER["SERVER_NAME"] . __WORK . "/" . substr($url, 1, strlen($url) - 1);
    }

    return $url;
}

function _file($url) {


    if ($url[0] == '/') {

        $url = __DIR . substr($url, 1, strlen($url) - 1);
    } else {

        $url = getcwd() . "/" . $url;
    }
    return $url;
}
