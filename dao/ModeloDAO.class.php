<?php

require_once __DIR__.'/../myconfig.php';



class ModeloDAO {

    public $pdo;

    public function __construct() {

        $this->pdo = ConnectFactory::getConnect();
    }

    public function load($id) {

        try {

            $stmt = $this->pdo->prepare("select * from table where id =:id");

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_OBJ);

            $aula = DAO::fetch('Modelo', $result);



            return $aula;
        } catch (PDOException $i) {

            echo "Erro: <code>" . $i->getMessage() . "</code>";
        }

        return false;
    }

    

}
