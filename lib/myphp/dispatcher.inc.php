<?php
global $dispatcher;
$isAction = true;

if($dispatcher){
    
   
     require_once(_file($dispatcher));
}