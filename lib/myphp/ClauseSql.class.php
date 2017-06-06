<?php

/**
 *
 * @revision 19/03/2014
 *
 */
class ClauseSql {

    /**
     * @param string $clausula_sql;
     * @access public
     */
    public $clausula_sql;

    /**
     * Cria  SQL INSERT
     *
     * @param string $tabela
     * @param array $campos
     * @param array $valores
     *
     * @access public
     */
    public function gera_insert($tabela, $campos, $valores) {

        $this->clausula_sql = "INSERT INTO " . $tabela . " (";

        $quantidade_campos = count($campos);

        for ($i = 0; $i < $quantidade_campos; ++$i) {
            $this->clausula_sql .= $campos[$i];

            if ($i < ($quantidade_campos - 1)) {
                $this->clausula_sql .= ", ";
            }
        }

        $this->clausula_sql .= ") VALUES (";

        for ($i = 0; $i < $quantidade_campos; ++$i) {


            $inserirVirgula = $i < ($quantidade_campos - 1);
            $this->clausula_sql .= ClauseSql :: escreve_valor($valores[$i], $inserirVirgula);
        }

        $this->clausula_sql .= ")";

        return $this->clausula_sql;
    }

    public function gera_valores($valores) {

        $this->clausula_sql = "(";

        $quantidade_campos = count($valores);

        for ($i = 0; $i < $quantidade_campos; ++$i) {


            $inserirVirgula = $i < ($quantidade_campos - 1);
            $this->clausula_sql .= ClauseSql :: escreve_valor($valores[$i], $inserirVirgula);
        }

        $this->clausula_sql .= ")";

        return $this->clausula_sql;
    }

    /**
     * Cria SQL UPDATE
     *
     * @param string $tabela
     * @param array $campos
     * @param array $valores
     * @param string $sentenca
     *
     * @access public
     */
    public function gera_update($tabela, $campos, $valores, $sentenca) {

        $this->clausula_sql = "UPDATE " . $tabela . " SET ";

        $quantidade_campos = count($campos);

        for ($i = 0; $i < $quantidade_campos; ++$i) {
            $this->clausula_sql .= $campos[$i] . " = ";


            $inserirVirgula = $i < ($quantidade_campos - 1);
            $this->clausula_sql .= ClauseSql :: escreve_valor($valores[$i], $inserirVirgula);
        }

        $this->clausula_sql .= " " . $sentenca . " ";

        return $this->clausula_sql;
    }

    /**
     * Retorna um valor formatado para se inserir na query SQL
     *
     * @param mix $valor
     * @param int $atual
     * @param int $total
     *
     * @access public
     * 
     * @revision 19/03/2014
     */
    public function escreve_valor($valor, $inserirVirgula) {



        if ($valor === null || $valor === NULL) {

            $retorno = 'NULL';
        } else if (is_bool($valor)) {

            $retorno = $valor;
        } else if (trim($valor) === '') {
            $retorno = 'NULL';
         } else if (($valor) === 'DEFAULT') { 
             $retorno = 'DEFAULT';
        } else if (!is_numeric($valor)) {
            $valor = ClauseSql::escape($valor);
            $retorno = '\'' . $valor . '\'';
        } else {
            $retorno = '\'' . $valor . '\'';
            #$retorno = $valor;
        }

        #logger($valor . '=>' . $retorno);


        if ($inserirVirgula) {
            $retorno .= ", ";
        }


        return ($retorno);
    }

    public static function escape($tring) {

        if ($tring !== null) {
            return pg_escape_string($tring);
        } else {
            return ($tring);
        }
    }

}
