<?php

require_once (dirname(__FILE__) . '/../addendum/annotations.php');
require_once (dirname(__FILE__) . '/Conf.php');
require_once (dirname(__FILE__) . '/ConnectFactory.class.php');
require_once (dirname(__FILE__) . '/ClauseSql.class.php');
require_once (dirname(__FILE__) . '/Table.inc.php');
require_once (dirname(__FILE__) . '/util.inc.php');
require_once (dirname(__FILE__) . '/MyForm.php');


class DAO {

    private static $DEBUG = __DAO_DEBUG;
    public static $SQL = '';

    const COLUMN = 1;
    const RELATION = 2;

    /**
     * Retorna a primeira linha da primeira coluna de uma consulta de um result
     * @param result result
     * @return mixed registro
     * @revision 10/12/2011
     */
    public static function getUniqueResult($stmt) {

        $valor = null;

        if ($stmt->rowCount() > 0) {

            $result = $stmt->fetch(PDO::FETCH_NUM);


            $valor = $result[0];
        }

        return $valor;
    }

    /**
     *
     * @revision 16/05/2012
     *
     */
    public static function save($object) {
        $inverse = false;
        if (func_num_args() == 1) {
            $reflection_class = new ReflectionAnnotatedClass($object);
            $table = DAO :: obterNomeTabela($reflection_class);
            $id = DAO :: obterNomeChave($reflection_class);
            $camposEspecificos = false;
        } else if (func_num_args() == 2) {
            $camposEspecificos = func_get_arg(1);
            $reflection_class = new ReflectionAnnotatedClass($object);
            $table = DAO :: obterNomeTabela($reflection_class);
            $id = DAO :: obterNomeChave($reflection_class);
        } else if (func_num_args() == 4) {
            $camposEspecificos = func_get_arg(1);
            $id = func_get_arg(2);
            $table = func_get_arg(3);
        } else if (func_num_args() == 5) {
            $camposEspecificos = func_get_arg(1);
            $id = func_get_arg(2);
            $table = func_get_arg(3);
            $inverse = func_get_arg(4);
        }


        //..

        $pdo = ConnectFactory::getConnect();
        $sql = new ClauseSql();


        $retorno = DAO :: obterCamposEDados($object, $camposEspecificos, $inverse);


        $campos = $retorno["campos"];
        $dados = $retorno["dados"];

        $insert = $sql->gera_insert($table, $campos, $dados);
        $insert .= " RETURNING " . $id . " ";
        if (DAO :: $DEBUG) {

            logger($insert);
        }
        #echo $insert;
        $stmt = $pdo->query($insert);


        // logger($pdo->errorInfo());



        if ($stmt != false) {

            $new_id = DAO :: getUniqueResult($stmt);

            $object->$id = $new_id;

            return $object;
        } else if (DAO :: $DEBUG) {


            logger_print($pdo->errorInfo());
            print_pre($pdo->errorInfo());

            return false;
        }
    }

    public static function saveArray($class, $array, $attr = false, $camposEspecificos = false, $commit = true) {


        $reflection_class = new ReflectionAnnotatedClass($class);
        $table = DAO :: obterNomeTabela($reflection_class);

        $clauseSql = new ClauseSql();
        $sql = '';
        $count = count($array);
        foreach ($array as $i => $a) {

            $a = DAO::TO_OBJECT($class, $a);


            if ($attr) {

                foreach ($attr as $k => $v) {

                    $xplode = explode('->', $k);


                    if (count($xplode) == 1) {
                        $a->$xplode[0] = $v;
                    } elseif (count($xplode) == 2) {
                        $a->$xplode[0]->$xplode[1] = $v;
                    } elseif (count($xplode) == 3) {
                        $a->$xplode[0]->$xplode[1]->$xplode[2] = $v;
                    }
                }
            }


            $retorno = DAO :: obterCamposEDados($a, $camposEspecificos);

            $campos = $retorno["campos"];
            $dados = $retorno["dados"];
            if ($i == 0) {
                $sql .= $clauseSql->gera_insert($table, $campos, $dados);
            } else {
                $sql .= $clauseSql->gera_valores($dados);
            }
            if ($i < ($count - 1)) {
                $sql.=',';
            }
        }//end for

        if ($commit) {

            if (DAO :: $DEBUG) {
                logger_print($sql);
            }

            $pdo = ConnectFactory::getConnect();
            $stmt = $pdo->query($sql);
        } else {
            self::$SQL .= $sql . ';';

            return $sql . ';';
        }
    }

    public static function TO_OBJECT($class, $array, $iterator = 0) {

        if (!is_array($array)) {
            return $array;
        }

        if ($class) {
            $reflection = new ReflectionClass($class);
            $obj = $reflection->newInstance($iterator == 0);
        } else {
            $obj = new stdClass();
        }
        foreach ($array as $key => $val) {
            if (is_array($val)) {

                $iterator++;
                $val = self::TO_OBJECT(ucfirst($key), $val, $iterator);
            }

            $obj->$key = $val;
        }

        return $obj;
    }

    /**
     *
     * @revision 16/05/2012
     *
     */
    public static function update($object) {
        $inverse = false;
        if (func_num_args() == 1) {
            $reflection_class = new ReflectionAnnotatedClass($object);
            $table = DAO :: obterNomeTabela($reflection_class);
            $id = DAO :: obterNomeChave($reflection_class);
            $camposEspecificos = false;
        } else if (func_num_args() == 2) {
            $camposEspecificos = func_get_arg(1);
            $reflection_class = new ReflectionAnnotatedClass($object);
            $table = DAO :: obterNomeTabela($reflection_class);
            $id = DAO :: obterNomeChave($reflection_class);
        } else if (func_num_args() == 4) {
            $camposEspecificos = func_get_arg(1);
            $id = func_get_arg(2);
            $table = func_get_arg(3);
        } else if (func_num_args() == 5) {
            $camposEspecificos = func_get_arg(1);
            $id = func_get_arg(2);
            $table = func_get_arg(3);
            $inverse = func_get_arg(4);
        }

        $pdo = ConnectFactory::getConnect();
        $sql = new ClauseSql();

        $retorno = DAO :: obterCamposEDados($object, $camposEspecificos, $inverse);

        $campos = $retorno["campos"];
        $dados = $retorno["dados"];

        # print_r($dados);

        $insert = $sql->gera_update($table, $campos, $dados, " where " . $id . "=" . $object->$id);
        $insert .= " RETURNING " . $id . " ";



        if (DAO :: $DEBUG) {
            # echo $insert;
            logger($insert);
        }
        $stmt = $pdo->query($insert);





        if ($stmt != false) {

            $new_id = DAO :: getUniqueResult($stmt);
            $object->$id = $new_id;
            return $object;
        } else if (DAO :: $DEBUG) {

            logger_print($pdo->errorInfo());
            print_pre($pdo->errorInfo());


            return false;
        }
    }

    /**
     *
     * @create 16/07/2015
     *
     */
    public static function inMerge($object) {
        $dados = func_num_args() == 2 ? func_get_arg(1) : false;

        self::merge($object, $dados, true);
    }

    /**
     *
     * @revision 10/12/2011
     *
     */
    public static function merge($object) {


        $dados = func_num_args() == 2 ? func_get_arg(1) : false;
        $inverse = func_num_args() == 3 ? func_get_arg(2) : false;

        $reflection_class = new ReflectionAnnotatedClass($object);



        $table = DAO :: obterNomeTabela($reflection_class);

        $id = DAO :: obterNomeChave($reflection_class);

        $valorId = $object->$id;


        $success = false;

        if ($valorId == null || $valorId == false) {

            $success = DAO :: save($object, $dados, $id, $table, $inverse);
        } else {

            $success = DAO :: update($object, $dados, $id, $table, $inverse);
        }

        if ($success) {

            return $success;
        } else {

            throw new Exception();
        }
    }

    /**
     *
     * @revision 10/12/2011
     *
     */
    public static function removeObject($object) {

        $reflection_class = new ReflectionAnnotatedClass($object);
        $table = DAO :: obterNomeTabela($reflection_class);
        $id = DAO :: obterNomeChave($reflection_class);
        $valorId = $object->$id;

        $pdo = ConnectFactory::getConnect();



        $query = "DELETE FROM " . $table . " WHERE " . $id . " = " . $valorId;


        #echo $query,'<br><br>';

        if (DAO :: $DEBUG) {
            echo $query;
        }

        $result = $pdo->query($query);

        return $result;
    }

    public static function remove($class, $conditions) {

        $reflection_class = new ReflectionAnnotatedClass($class);
        $table = DAO :: obterNomeTabela($reflection_class);


        $where = '';
        if (!is_array($conditions)) {

            $id = DAO :: obterNomeChave($reflection_class);
            $conditions = pg_escape_string($conditions);
            $where = $id . " = " . $conditions;
        } else {

            $columns = DAO::getColumns($class, $reflection_class);

            $sql = new ClauseSql();
            $cont = 0;
            foreach ($conditions as $NomeCampo => $valorCampo) {

                if ($cont > 0) {
                    $where.= ' AND ';
                }

                $where.= $columns[$NomeCampo] . ' = ' . $sql->escreve_valor($valorCampo, false);

                $cont++;
            }
        }

        $pdo = ConnectFactory::getConnect();

        $query = "DELETE FROM " . $table . " where " . $where;

        #echo $query,'<br><br>';

        if (DAO :: $DEBUG) {
            logger($query);
        }


        $result = $pdo->query($query);

        return $result;
    }

    /**
     *
     * @revision 04/03/2014
     *
     */
    public static function Count($class, $conditions = false) {

        $reflection_class = new ReflectionAnnotatedClass($class);
        $table = DAO :: obterNomeTabela($reflection_class);


        $where = '';
        if (is_array($conditions)) {
            $where .= " WHERE ";
            $columns = DAO::getColumns($class, $reflection_class);

            $sql = new ClauseSql();
            $cont = 0;
            foreach ($conditions as $NomeCampo => $valorCampo) {

                if ($cont > 0) {
                    $where.= ' AND ';
                }

                $where.= $columns[$NomeCampo] . ' = ' . $sql->escreve_valor($valorCampo, false);

                $cont++;
            }
        }

        $pdo = ConnectFactory::getConnect();

        $query = "select COUNT (*) from " . $table . $where;

        # echo $query, '<br><br>';

        if (DAO :: $DEBUG) {
            logger($query);
        }


        $stmt = $pdo->query($query);


        $object = $stmt->fetch(PDO::FETCH_OBJ);



        return $object->count;
    }

    public static function querySimple($sql, $params, $all = true) {

        try {
            # var_dump($params);
            $pdo = ConnectFactory::getConnect();

            $stmt = $pdo->prepare($sql);
            if ($params) {
                foreach ($params as $param => $value) {

                    if (!is_numeric($params[$param])) {

                        $stmt->bindParam($param, $params[$param], PDO::PARAM_STR);
                    } else {

                        $stmt->bindParam($param, $params[$param], PDO::PARAM_INT);
                    }
                }
            }

            $stmt->execute();

            if (DAO :: $DEBUG) {
                logger_print($stmt->errorInfo());
            }

            if ($all) {
                $result = $stmt->fetchAll(PDO::FETCH_OBJ);
                $retorno = $result;
            } else {
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                $retorno = $result;
            }

            return $retorno;
        } catch (PDOException $i) {

            echo("Erro: <code>" . $i->getMessage() . "</code>");
            
            echo "<br><br>";
        }

        return false;
    }

    public static function query($class, $sql, $params, $all = true) {

        try {
            # var_dump($params);
            $pdo = ConnectFactory::getConnect();

            $stmt = $pdo->prepare($sql);
            if ($params) {
                foreach ($params as $param => $value) {

                    if (!is_numeric($params[$param])) {
                        $stmt->bindParam($param, $params[$param], PDO::PARAM_STR);
                    } else {
                        $stmt->bindParam($param, $params[$param], PDO::PARAM_INT);
                    }
                }
            }

            $stmt->execute();
            if ($all) {
                $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            } else {
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                $retorno = DAO::fetch($class, $result);
                return $retorno;
            }



            #return $result;
            $retorno = array();
            $i = 0;

            foreach ($result as $row) {

                $retorno[$i] = DAO::fetch($class, $row);
                $i++;
            }


            return $retorno;
        } catch (PDOException $i) {
            if (DAO :: $DEBUG) {
                print_pre($i);
            }
            logger("Erro: <code>" . $i->getMessage() . "</code>");
        }

        return false;
    }

    /**
     *
     * @revision 04/03/2014
     *
     */
    public static function load($class, $conditions) {

        $reflection_class = new ReflectionAnnotatedClass($class);
        $table = DAO :: obterNomeTabela($reflection_class);


        $where = '';
        if (!is_array($conditions)) {

            $id = DAO :: obterNomeChave($reflection_class);
            $where = $id . " = " . pg_escape_string($conditions);
        } else {

            $columns = DAO::getColumns($class, $reflection_class);

            $sql = new ClauseSql();
            $cont = 0;
            foreach ($conditions as $NomeCampo => $valorCampo) {

                if ($cont > 0) {
                    $where.= ' AND ';
                }

                $where.= $columns[$NomeCampo] . ' = ' . $sql->escreve_valor($valorCampo, false);

                $cont++;
            }
        }

        $pdo = ConnectFactory::getConnect();

        $query = "select * from " . $table . " where " . $where;

        #echo $query,'<br><br>';

        if (DAO :: $DEBUG) {
            logger($query);
        }


        $stmt = $pdo->query($query);


        $object = $stmt->fetch(PDO::FETCH_OBJ);





        return DAO :: fetch($class, $object, $reflection_class);
    }

    /**
     *
     * @revision 10/12/2011
     *
     */
    public static function fetch2($class, $fetch) {

        if ($fetch != null && $fetch != false) {

            $class_vars = get_object_vars($fetch);

            if (func_num_args() == 3) {
                $reflection_annotated = func_get_arg(2);
            } else {
                $reflection_annotated = new ReflectionAnnotatedClass($class);
            }

            $reflection = new ReflectionClass($class);
            $object = $reflection->newInstance();
            $propriedades = $reflection_annotated->getProperties();

            for ($i = 0; $i < count($class_vars); $i++) {

                $coluna = key($class_vars);
                $valor = $class_vars[$coluna];

                foreach ($propriedades as $propriedade) {
                    if ($propriedade->hasAnnotation('Column')) {

                        //tenta obter o nome da coluna : @Column("name");
                        $column = $propriedade->getAnnotation('Column')->value;

                        if ($column == "" || $column == null) {
                            //tenta obter o nome da coluna : @Column(name="name");
                            $column = $propriedade->getAnnotation('Column')->name;
                        }
                        if ($column == "" || $column === null) {

                            $column = $propriedade->name;
                        }

                        if ($column == $coluna) {

                            $name = $propriedade->name;
                            $object->$name = $valor;

                            //redefine valores de acordo com seus tipos
                            if ($propriedade->hasAnnotation('Type')) {
                                $type = $propriedade->getAnnotation('Type')->value;

                                if ($type == 'Boolean') {
                                    if ($object->$name == 1) {
                                        $object->$name = true;
                                    } else {
                                        $object->$name = false;
                                    }
                                }
                            }// end if redefine

                            break;
                        }
                    }
                }//end foreach



                next($class_vars);
            }//end for



            return $object;
        }

        return false;
    }

    /**
     *
     * @revision 02/03/2013
     *
     */
    public static function fetchAll($class, $result) {
        $list = array();
        $i = 0;
        foreach ($result as $r) {

            $list[$i] = DAO::fetch($class, $r);

            $i++;
        }

        return $list;
    }

    /**
     *
     * @revision 10/12/2011
     *
     */
    public static function fetch($class, $fetch) {

        if ($fetch != null && $fetch != false) {

            if (func_num_args() == 3) {
                $reflection_annotated = func_get_arg(2);
            } else {
                $reflection_annotated = new ReflectionAnnotatedClass($class);
            }

            $reflection = new ReflectionClass($class);
            $object = $reflection->newInstance();

            $array = get_object_vars($fetch);
            #print_r($array);
            if ($array != false && $array != null) {

                $propriedades = $reflection_annotated->getProperties();
                foreach ($propriedades as $propriedade) {
                    if ($propriedade->hasAnnotation('Column')) {

                        //tenta obter o nome da coluna : @Column("name");
                        $column = $propriedade->getAnnotation('Column')->value;

                        if ($column == "" || $column == null) {

                            //tenta obter o nome da coluna : @Column(name="name");
                            $column = $propriedade->getAnnotation('Column')->name;
                        }
                        if ($column == "" || $column === null) {

                            $column = $propriedade->name;
                        }

                        if (array_key_exists($column, $array)) {

                            $name = $propriedade->name;
                            $object->$name = $array[$column];


                            //redefine valores de acordo com seus tipos
                            if ($propriedade->hasAnnotation('Type')) {
                                $type = $propriedade->getAnnotation('Type')->value;


                                if ($type == 'Boolean') {

                                    #echo '<br>=>',$propriedade;

                                    if ($object->$name == 1) {
                                        $object->$name = true;
                                    } else {
                                        $object->$name = false;
                                    }
                                }
                            }// end if redefine
                        }
                    }
                }
                $object->others = $fetch;
                return $object;
            } else {

                return false;
            }
        }

        return false;
    }

    /**
     *
     * @revision 04/03/2014
     *
     */
    public static function getColumns($class, $reflection = false) {

        $retorno = array();

        $reflection_class = $reflection == false ? new ReflectionAnnotatedClass($class) : $reflection;


        $retorno['__TABLE__'] = DAO :: obterNomeTabela($reflection_class);

        $propriedades = $reflection_class->getProperties();
        if ($reflection_class->hasAnnotation('Table')) {

            foreach ($propriedades as $propriedade) {

                if ($propriedade->hasAnnotation('Column')) {


                    $column = $propriedade->getAnnotation('Column')->value;
                    if ($column == "" || $column == null) {

                        $column = $propriedade->getAnnotation('Column')->name;
                    }
                    if ($column == "" || $column === null) {

                        $column = $propriedade->name;
                    }

                    $retorno[$propriedade->name] = $column;
                } else if ($propriedade->hasAnnotation('Relation')) {


                    #$target = $propriedade->getAnnotation('Relation')->target;
                    $attribute = $propriedade->getAnnotation('Relation')->attribute;
                    $column_target = $propriedade->getAnnotation('Relation')->column;

                    if ($column_target == false) {
                        $column = $attribute;
                    } else {
                        $column = $column_target;
                    }



                    $retorno[$propriedade->name] = pg_escape_string($column);
                }
            }//end-foreach
        }


        return $retorno;
    }

    /**
     *
     * @revision 04/03/2014
     *
     */
    private static function obterCamposEDados($object, $camposEspecificos = false, $inverse = false) {

        if ($inverse) {
            return self::obterCamposEDadosPorAtributo($object, $camposEspecificos);
        } else {
            return self::obterCamposEDadosPorAnotacao($object, $camposEspecificos);
        }
    }

    /**
     * TODO - Não terminado ;(
     * @param type $object
     * @param type $camposEspecificos
     * @return array
     */
    private static function obterCamposEDadosPorAtributo($object) {

        $reflection_class = new ReflectionAnnotatedClass($object);
        $reflect = new ReflectionClass($object);

        $propriedades = $reflection_class->getProperties();
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);


        $campos = array();
        $dados = array();

        $j = 0;
        if ($reflection_class->hasAnnotation('Table')) {
            foreach ($props as $prop) {

                $propriedade = new ReflectionAnnotatedProperty($object, $prop->getName());

                

                if ($propriedade->hasAnnotation('Column') && !$propriedade->hasAnnotation('AutoGenerator')) {
                    #logger($propriedade->name." = > Entrou :)");

                    $column = $propriedade->getAnnotation('Column')->value;
                    if ($column == "" || $column == null) {

                        $column = $propriedade->getAnnotation('Column')->name;
                    }
                    if ($column == "" || $column === null) {

                        $column = $propriedade->name;
                    }

                    $getter = 'get' . ucfirst($column);
                    $value = $object->$getter();

                    $value = $value === NULL ? 'DEFAULT' : $value;


                    $campos[$j] = $column;
                    $dados[$j] = $value;

                    //redefine valores de acordo com seus tipos
                    if ($propriedade->hasAnnotation('Type')) {
                        $type = $propriedade->getAnnotation('Type')->value;

                        if ($type == 'Boolean') {
                            #echo "<br>",$propriedade;
                            if ($dados[$j] === 'false' || $dados[$j] === false || $dados[$j] === 0 || $dados[$j] === '0') {

                                $dados[$j] = 'false';

                                #echo 'false';
                            } else if ($dados[$j] === 'true' || $dados[$j] === true || $dados[$j] === 1 || $dados[$j] === '1') {

                                $dados[$j] = 'true';

                                # echo 'true';
                            }
                        } else if ($type == 'Date') {
                            #$dados[$j] = brDateToW3C($dados[$j]);
                        }
                    }// end if redefine


                    $j++;
                }
            }
        }

        $retorno["dados"] = $dados;
        $retorno["campos"] = $campos;

        return $retorno;
    }

    private static function obterCamposEDadosPorAnotacao($object, $camposEspecificos) {


        $reflection_class = new ReflectionAnnotatedClass($object);

        $campos = array();
        $dados = array();
        #$arr = get_object_vars($object);
        $j = 0;

        $propriedades = $reflection_class->getProperties();
        if ($reflection_class->hasAnnotation('Table')) {

            foreach ($propriedades as $propriedade) {

                if ($propriedade->hasAnnotation('Column') && !$propriedade->hasAnnotation('AutoGenerator')) {



                    if ($camposEspecificos === false || in_array($propriedade->name, $camposEspecificos)) {
                       
                        $column = $propriedade->getAnnotation('Column')->value;
                        if ($column == "" || $column == null) {

                            $column = $propriedade->getAnnotation('Column')->name;
                        }
                        if ($column == "" || $column === null) {

                            $column = $propriedade->name;
                        }

                        $getter = 'get' . ucfirst($column);



                        $value = $object->$getter();

                        $value = $value === NULL ? 'DEFAULT' : $value;


                        $campos[$j] = $column;
                        $dados[$j] = $value;

                        //redefine valores de acordo com seus tipos
                        if ($propriedade->hasAnnotation('Type')) {
                            $type = $propriedade->getAnnotation('Type')->value;

                            if ($type == 'Boolean') {
                                #echo "<br>",$propriedade;
                                if ($dados[$j] === 'false' || $dados[$j] === false || $dados[$j] === 0 || $dados[$j] === '0') {

                                    $dados[$j] = 'false';

                                    #echo 'false';
                                } else if ($dados[$j] === 'true' || $dados[$j] === true || $dados[$j] === 1 || $dados[$j] === '1') {

                                    $dados[$j] = 'true';

                                    # echo 'true';
                                }
                            } else if ($type == 'Date') {
                                #$dados[$j] = brDateToW3C($dados[$j]);
                            }
                        }// end if redefine


                        $j++;
                        #**************************************************************************************************
                    } else {
                        # logger($propriedade->name." = > Nao Entrou :(");
                    }
                } else if ($propriedade->hasAnnotation('Relation')) {




                    if ($camposEspecificos == false || in_array($propriedade->name, $camposEspecificos)) {

                        #logger($propriedade->name." = > Entrou :)");

                        $target = $propriedade->getAnnotation('Relation')->target;
                        $attribute = $propriedade->getAnnotation('Relation')->attribute;

                        $column_target = $propriedade->getAnnotation('Relation')->column;
                        $getter1 = 'get' . ucfirst($propriedade->name);
                        $campos[$j] = $column_target;

                        if ($attribute == false) {
                            $getter2 = 'get' . ucfirst($column_target);
                        } else {
                            $getter2 = 'get' . ucfirst($attribute);
                        }
                        # var_dump($object);
                        #echo "->".$getter1."=>".$getter2.'<br>';

                        $dados[$j] = $object->$getter1()->$getter2();

                        $j++;
                    } else {
                        #logger($propriedade->name." = > Nao Entrou :(");
                    }
                }
            }
        }

        $retorno["dados"] = $dados;
        $retorno["campos"] = $campos;

        return $retorno;
    }

    /**
     *
     * @revision 10/12/2011
     *
     */
    public static function obterNomeTabela($reflection_class) {

        $table = $reflection_class->getAnnotation('Table')->value;
        if ($table == "" || $table == null) {

            $table = $reflection_class->getAnnotation('Table')->name;
        }
        if ($table == "" || $table == null) {

            $table = $reflection_class->name;
        }

        return $table;
    }

    /**
     *
     * @revision 10/12/2011
     *
     */
    public static function obterNomeChave($reflection_class) {

        $propriedades = $reflection_class->getProperties();

        foreach ($propriedades as $propriedade) {

            if ($propriedade->hasAnnotation('Id')) {

                $id = $propriedade->getAnnotation('Id')->value;
                if ($id == "" || $id == null) {

                    $id = $propriedade->getAnnotation('Id')->name;
                }
                if ($id == "" || $id === null) {

                    $id = $propriedade->name;
                }

                #$getter = 'get'.ucfirst($id);
                #$value= $object->$getter();
            }
        }

        return $id;
    }

    /**
     *
     * @revision 11/12/2014
     *
     */
    public static function getType($reflection_class, $nomePropriedade) {

        $propriedade = $reflection_class->getProperty($nomePropriedade);
        if ($propriedade != null) {
            if ($propriedade->hasAnnotation('Column')) {
                return DAo::COLUMN;
            }
            if ($propriedade->hasAnnotation('Relation')) {
                return DAo::RELATION;
            }
        }
        return false;
    }

    /**
     *
     * @revision 11/12/2014
     *
     */
    public static function obterNomeColunaOuRelacao($reflection_class, $nomePropriedade) {

        $propriedade = $reflection_class->getProperty($nomePropriedade);

        if ($propriedade != null) {

            if ($propriedade->hasAnnotation('Column')) {
                return self::obterNomeColuna($reflection_class, $nomePropriedade);
            }
            if ($propriedade->hasAnnotation('Relation')) {
                return self::obterNomeColunaRelacao($reflection_class, $nomePropriedade);
            }
        }

        return false;
    }

    /**
     *
     * @revision 11/12/2014
     *
     */
    public static function obterNomeColuna($reflection_class, $nomePropriedade) {


        $propriedade = $reflection_class->getProperty($nomePropriedade);

        if ($propriedade != null) {

            $column = $propriedade->getAnnotation('Column')->value;

            if ($column == "" || $column == null) {

                $column = $propriedade->getAnnotation('Column')->name;
            }
            if ($column == "" || $column === null) {

                $column = $propriedade->name;
            }
            return $column;
        } else {
            return false;
        }
    }

    /**
     *
     * @revision 02/12/2014
     *
     */
    public static function obterNomeColunaRelacao($reflection_class, $nomePropriedade) {

        $propriedade = $reflection_class->getProperty($nomePropriedade);

        #print_pre($propriedade);

        if ($propriedade != null) {

            $name = $propriedade->getAnnotation('Relation')->column;
            if ($name == "" || $name === null) {


                $name = $propriedade->name;
            }

            return $name;
            #$getter = 'get'.ucfirst($id);
            #$value= $object->$getter();
        }
        return $nomePropriedade;
    }

    /**
     *
     * @revision 02/12/2014
     *
     */
    public static function obterNomeJoinColunaRelacao($reflection_class, $nomePropriedade) {

        $propriedade = $reflection_class->getProperty($nomePropriedade);

        #print_pre($propriedade);

        if ($propriedade != null) {

            $name = $propriedade->getAnnotation('Relation')->attribute;
            if ($name == "" || $name === null) {


                $name = self::obterNomeColunaRelacao($reflection_class, $nomePropriedade);
            }


            return $name;
            #$getter = 'get'.ucfirst($id);
            #$value= $object->$getter();
        }
        return $nomePropriedade;
    }

    /**
     *
     * @revision 02/12/2014
     *
     */
    public static function obterNomeEntityPorPropriedade($reflection_class, $nomePropriedade) {

        $propriedade = $reflection_class->getProperty($nomePropriedade);

        #print_pre($propriedade);

        if ($propriedade != null) {

            $name = $propriedade->getAnnotation('Relation')->target;
            if ($name == "" || $name === null) {


                $name = $propriedade->name;
            }


            return $name;
            #$getter = 'get'.ucfirst($id);
            #$value= $object->$getter();
        }
        return false;
    }

    public static function lista($class, $conditions = false, $orderby = false) {

        $retorno = array();

        $reflection_class = new ReflectionAnnotatedClass($class);

        $table = DAO::obterNomeTabela($reflection_class);

        $pdo = ConnectFactory::getConnect();
        $sql = 'Select * From ' . $table;



        if (is_array($conditions)) {

            $columns = DAO::getColumns($class, $reflection_class);
            $where = " WHERE ";
            $clauseSql = new ClauseSql();
            $cont = 0;
            foreach ($conditions as $NomeCampo => $valorCampo) {

                if ($cont > 0) {
                    $where.= ' AND ';
                }

                $where.= $columns[$NomeCampo] . ' = ' . $clauseSql->escreve_valor($valorCampo, false);

                $cont++;
            }

            $sql .=$where;
        }

        if ($orderby) {
            $sql .= ' order by ' . $orderby;
        }


        $stmt = $pdo->query($sql);

        if (DAO :: $DEBUG) {

            logger($sql);
        }

        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        $i = 0;
        foreach ($result as $row) {

            $retorno[$i] = DAO::fetch($class, $row);
            $i++;
        }

        return $retorno;
    }

}
