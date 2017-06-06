<?php

function myautoload($pClassName) {

    $sources = ['/models/', '/dao/'];
    foreach ($sources as $source) {
        $f = _file($source . $pClassName . ".class.php");
        
        if (file_exists($f)) {
            require_once $f;
        }
    }

    
}

spl_autoload_register("myautoload");
