<?php

$tempFormValue = false;

function formInput($field, $func = false, $value = true) {

    global $tempFormValue;

    $explode = explode('->', $field);

    $count = count($explode);
    if ($count == 1) {
        $v = $GLOBALS[$explode[0]];
    } else if ($count == 2) {
        $g = $GLOBALS[$explode[0]];
        $array = (array) $g;
        $v = $array[$explode[1]];
    } else if ($count == 3) {
        $g = $GLOBALS[$explode[0]];
        $array = (array) $g;
        $array2 = (array) $array[$explode[1]];
        $v = $array2[$explode[2]];
        #$v = 's';
    } else if ($count == 4) {
        $g = $GLOBALS[$explode[0]];
        $array = (array) $g;
        $array2 = (array) $array[$explode[1]];
        $array3 = (array) $array2[$explode[2]];
        $v = $array2[$explode[3]];
    }

    $str = '';
    $str.=" name='$field'";

    $id = str_replace('->', "_", $field);

    $str.=" id='$id'";

    if ($value) {

        if ($func) {
            $v = $func($v);
        }

        $str.=" value='$v'";
    } else {
        $tempFormValue = $v;
    }




    return $str;
}

function formTextArea($field, $func=false) {

    return formInput($field, $func, false);
}

function formValue() {

    global $tempFormValue;

    return $tempFormValue;
}

function formSelect($field) {

    $str = '';
    $str.=" name='$field'";

    $id = str_replace('->', "_", $field);

    $str.=" id='$id'";



    return $str;
}
