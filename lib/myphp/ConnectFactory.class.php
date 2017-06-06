<?php

require_once (dirname(__FILE__) . '/Conf.php');

/**
 *
 * @revision 10/12/2011
 *
 */
class ConnectFactory {

    private $pdo;
    public static $instance;

    private function __construct() {
        
    }

    public static function getConnect() {    
        
        global $myconnections;

        if (!isset(self::$instance)) {

            self::$instance = new PDO($myconnections['default']);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           
        }        
        
        return self::$instance;
    }

    public function close() {

        $this->pdo = null;
    }

}
