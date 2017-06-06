<?

require_once (dirname(__FILE__) . '/../addendum/annotations.php');
require_once (dirname(__FILE__) . '/Conf.php');



$error_messages = array();


/* @var $go type String */
$go;
$dispatcher = false;
$default_method;
$params_method = false;
$_RESPONSE = array();
$_FORM = array();

function Controller_execute($controller_name) {

    global $go, $controller, $params_method, $default_method, $_FORM;

    $go = false;


    $reflection = new ReflectionClass($controller_name);
    $controller = $reflection->newInstance();

    $_FORM = array_merge($_GET, $_POST);



    if (isset($_FORM["go"])) {
        $method = $_FORM["go"];
    } else if ($default_method != false) {

        $method = $default_method;
    }

    if (isset($controller) && isset($method)) {

        if (!__OTIMIZE) {
            $reflection_method = new ReflectionAnnotatedMethod($controller, $method);
            $preAction = $reflection_method->getAnnotation('PreAction');
            if ($preAction) {

                $mt = $preAction->value;
                $controller->$mt();
            }
        }

        $__method = '__' . $method;
         if (method_exists($controller, '__initialize')) {
            $controller->__initialize();
        }
        if (method_exists($controller, $__method)) {
            $controller->$__method();
        }
    }

    foreach ($_FORM as $chave => $valor) {

        $array = explode("->", $chave);

        $count = count($array);

        if ($count == 1) {

            $a0 = $array[0];

            $controller->$a0 = $valor;
        } else if ($count == 2) {

            $a0 = $array[0];
            $a1 = $array[1];

            $controller->$a0->$a1 = $valor;
        } else if ($count == 3) {

            $a0 = $array[0];
            $a1 = $array[1];
            $a2 = $array[2];


            $controller->$a0->$a1->$a2 = $valor;
        } else if ($count == 4) {

            $a0 = $array[0];
            $a1 = $array[1];
            $a2 = $array[2];
            $a3 = $array[3];

            $controller->$a0->$a1->$a2->$a3 = $valor;
        }
    }


    if (isset($method) && isset($controller)) {

        $go = $method;


        if (method_exists($controller, 'methodCheck')) {
            $controller->methodCheck($go);
        }

        $controller->$method();
    }
}

function Controller_defaultMethod($method, $params = false) {

    global $default_method, $params_method;


    if (!$default_method) {
        $default_method = $method;
        $params_method = $params;
    }
}

function dispatcher() {

    global $controller, $dispatcher;

    $class_vars = get_object_vars($controller);

    //print_r($class_vars);

    $count = count($class_vars);
    for ($i = 0; $i < $count; $i++) {

        $chave = key($class_vars);
        $valor = $class_vars[$chave];


        global $$chave;

        $$chave = $valor;


        next($class_vars);
    }//end for

    if (func_num_args() == 1) {
        $dispatcher = func_get_arg(0);
    }

    methods_call_dispatcher();
}

function Controller_set($ctrl, $method_default = false) {

    if ($method_default) {
        Controller_defaultMethod($method_default);
    }

    require_once _file('/controllers/' . $ctrl . '.php');

    Controller_execute($ctrl);
}
