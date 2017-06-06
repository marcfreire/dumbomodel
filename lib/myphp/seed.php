<?php

require_once __DIR__ . '../../../myconfig.php';


$class = $_GET['class'];

if ($class == 'false') {
    echo "<h1>Use seed:Class</h1>";
} else {
    $i = @include_once _file('/dao/seeds/' . $class . 'Seeder.php');

    if ($i) {
        $r = new ReflectionClass($class . 'Seeder');
        $c = $r->newInstance();

        $c->run();

        echo "<h1>Seed executed successfully!</h1>";
    }else{
        echo "<h1>Error : Seed not found</h1>";
    }
}