<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Accessors
 *
 * @author marcos
 */
trait Accessors {
    
    
      public function __call($name, $args) {

        $field = substr($name, 3, strlen($name));
        $field = strtolower($field);

        if (substr($name, 0, 3) == 'set') {

            $this->$field = $args[0];
        } elseif (substr($name, 0, 3) == 'get') {


            return $this->$field;
        } else {
            echo "Error : Call to undefined method $name";
        }
    }
}
