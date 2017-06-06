<?php

require_once __DIR__.'/../myconfig.php';


/**
 *
 * @Table("table");
 *
 *
 */
class Modelo {

    /**
     *
     * @Id
     * @Column
     * @AutoGenerator
     */
    public $idmodelo;
    
     /**
     * @Column
     */
    public $stdelete;

    /**
     * @Column
     */
    public $datacadastro;

   

    /**
     * @Column
     */
    public $obs;

  

    

    public function __construct() {
       

      
    }

   
  
    

}
