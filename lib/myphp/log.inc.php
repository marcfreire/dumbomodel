<?php

define("INFO", "INFO");
define("NOTICE", "NOTICE");
define("ALERT", "ALERT");
define("ERROR", "ERROR");

function logger() {

    $msg = "";
    $type = "";
    $pre = '';

    if (func_num_args() == 0) {

        $msg = __LINE;
        $type = INFO;
    } else if (func_num_args() == 1) {
        $msg = func_get_arg(0);
        $type = INFO;
    } else if (func_num_args() == 2) {

        $type = func_get_arg(0);
        $msg = func_get_arg(1);
    } else if (func_num_args() == 3) {

        $type = func_get_arg(0);
        $msg = func_get_arg(1);
        $pre = func_get_arg(2);
    }

//pega o path completo de onde esta executanto
    $caminho_atual = getcwd();


    if (defined('DIR_LOG')) {
        //muda o contexto de execucao para a pasta logs   
        chdir(DIR_LOG);
    }
    $data = date("d-m-y");
    $hora = date("H:i:s");
    $ip = $_SERVER['REMOTE_ADDR'];

//Nome do arquivo:
    $arquivo = $pre."Logger_" .  $data . ".txt";


    $explode = explode("/", $_SERVER['PHP_SELF']);
    $pagina = end($explode);

    $file = $pagina . " : " . __LINE__;

//Texto a ser impresso no log:
    $texto = "$type : [$file][$hora][$ip] => $msg \n";

    $manipular = fopen("$arquivo", "a+b");
    fwrite($manipular, $texto);
    fclose($manipular);

//Volta o contexto de execução para o caminho em que estava antes
    chdir($caminho_atual);
}

function logger_print() {

    if (func_num_args() == 1) {
        logger(print_r(func_get_arg(0), true));
    }
    if (func_num_args() == 2) {
        logger(func_get_arg(0), print_r(func_get_arg(1),true));
    }
    if (func_num_args() == 3) {
        logger(func_get_arg(0), print_r(func_get_arg(1),true), func_get_arg(2));
    }
}

function print_pre($obj) {
    echo "<pre>";
    print_r($obj);
    echo "</pre>";
}

function var_pre($obj) {
    echo "<pre>";
    var_dump($obj);
    echo "</pre>";
}
