<?php

date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
require_once (_file('/models/Usuario.class.php'));
session_start();
require_once __DIR__ . '/../myconfig.php';
require_once (_file('/util/Constantes.inc.php'));
require_once (_file('/dao/UsuarioDAO.class.php'));

require_once (_file('/vendor/autoload.php'));

require_once (_file('/dao/MetadadosDAO.class.php'));
require_once (_file('/models/Metadados.class.php'));

$codAlert = filter_input(INPUT_GET, 'msg');

//**************************************************************************************
define("MSG_NOTIFICACAO_SALVO_SUCESSSO", 'Salvo com sucesso');
define("MSG_NOTIFICACAO_EXCLUIR_SUCESSSO", 'Excluído com sucesso');

//**************************************************************************************

if (isset($_COOKIE['COOKIE_QT_NOTIFICACOES'])) {
    $qtNotificacoes = $_COOKIE['COOKIE_QT_NOTIFICACOES'];
} else {
    $qtNotificacoes = 0;
}



function getKeySlideShare($url, $api_key, $secret) {

    $time = time();

    $retorno = new stdClass();

    $link = 'https://www.slideshare.net/api/2/get_slideshow?api_key=' . $api_key . '&ts=' . $time . '&hash=' . sha1($secret . $time) . '&slideshow_url=' . $url;

    $xml = simplexml_load_file($link);



    $retorno->key = (string) $xml->SecretKey;
    $retorno->icone_m = (string) $xml->ThumbnailXXLargeURL;
    $retorno->icone = (string) $xml->ThumbnailSmallURL;

    return $retorno;
}

function time_elapsed_string($datetime, $full = false) {


    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'ano',
        'm' => 'mês',
        'w' => 'semana',
        'd' => 'dia',
        'h' => 'hora',
        'i' => 'minutos',
        's' => 'segundo',
    );
    foreach ($string as $k => &$v) {
        $v_copy = $v;
        if ($diff->$k) {

            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            if ($v_copy == 'mês')
                $v = str_replace('mêss', 'meses', $v);
        } else {
            unset($string[$k]);
        }
    }

    if (!$full)
        $string = array_slice($string, 0, 1);

    $r = $string ? implode(', ', $string) . ' atrás' : 'agora mesmo';


    return $r;
}

function printSeguir($seguindo, $nome, $id) {
    if ($seguindo) {

        $html = <<<HTML
             
           <button class="active seguir cg ts fx" data-nome="${nome}" data-id="${id}">
                 <span class="h ago "></span> Seguindo
           </button>           
HTML;
    } else {

        $html = <<<HTML
             
           <button class="seguir cg ts fx" data-nome="${nome}" data-id="${id}">
                 <span class="h vc"></span> Seguir
           </button>           
HTML;
    }
    return $html;
}


function MontarLink($texto){
    
       if (!is_string ($texto))
           return $texto;

    $er = "/(https:\/\/(www\.|.*?\/)?|http:\/\/(www\.|.*?\/)?|www\.)([a-zA-Z0-9]+|_|-)+(\.(([0-9a-zA-Z]|-|_|\/|\?|=|&)+))+/i";

    $texto = preg_replace_callback($er, function($match){
        $link = $match[0];

        //coloca o 'http://' caso o link não o possua
        $link = (stristr($link, "http") === false) ? "http://" . $link : $link;

        //troca "&" por "&", tornando o link válido pela W3C
        $link = str_replace ("&", "&amp;", $link);

        return "<a href=\"" . ($link) . "\" target=\"_blank\">". ((strlen($link) > 60) ? substr ($link, 0, 25). "...". substr ($link, -15) : $link) ."</a>";
    },$texto);

    return $texto;

}
