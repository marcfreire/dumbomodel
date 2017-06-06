<?php

date_default_timezone_set("Brazil/East");

function count_result($result) {

    $count = $result[0]->count;
    return $count;
}

function cloneAttr($obj1, $obj2, $attr) {


    foreach ($attr as $a) {
        $obj1->$a = $obj2->$a;
    }
}

function generate_password($size = 32, $upper = true, $numbers = true, $symbols = true) {

    // Caracteres de cada tipo
    $lmin = 'abcdefghijklmnopqrstuvwxyz';
    $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $num = '1234567890';
    $simb = '!@#$%*-';

// Variáveis internas
    $retorno = '';
    $caracteres = '';

// Agrupamos todos os caracteres que poderão ser utilizados
    $caracteres .= $lmin;
    if ($upper)
        $caracteres .= $lmai;
    if ($numbers)
        $caracteres .= $num;
    if ($symbols)
        $caracteres .= $simb;

// Calculamos o total de caracteres possíveis
    $len = strlen($caracteres);

    for ($n = 1; $n <= $size; $n++) {
// Criamos um número aleatório de 1 até $len para pegar um dos caracteres
        $rand = mt_rand(1, $len);
// Concatenamos um dos caracteres na variável $retorno
        $retorno .= $caracteres[$rand - 1];
    }


    return $retorno;
}

function getDateTimeNow() {
    $date_now = new DateTime();

    return $date_now->format(DATE_W3C);
}

function formatValue($v) {
    return str_replace(',', '.', $v);
}

function printBRValue($v) {

    return str_replace('.', ',', $v);
}

function stripdecode_url($string) {
    /*
     * 
     * para habilitar no PHP7
     * apt-get install php7.0-xml
     * apt-get install php7.0-xmlrpc
     */

    return strtolower(preg_replace("[^a-zA-Z0-9-]", "-", strtr(utf8_decode(trim(strip_tags($string))), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ "), "aaaaeeiooouuncAAAAEEIOOOUUNC--")));
}

function getCodeYoutube($str) {


    $regex = "#youtu(be.com|.b)(/v/|/watch\\?v=|e/|/watch(.+)v=)(.{11})#";

    preg_match_all($regex, $str, $matches);

    if (isset($matches[4][0])) {
        return $matches[4][0];
    } else {

        return $str;
    }
}

function check_http($string) {

    if (strpos($string, 'http') !== false) {
        return $string;
    } else {
        return 'http://' . $string;
    }
}

function getIP() {

    return $_SERVER['REMOTE_ADDR'];


    #return isset($vIP[5]) < 5 ? false : $vIP;
}

function chopExtension($filename) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    return preg_replace('/\.' . preg_quote($ext, '/') . '$/', '', $filename);
}

function getNameScript() {
    return chopExtension(basename($_SERVER['PHP_SELF']));
}

function suspensionPoints($name, $max = 20) {

    if (strlen($name) > $max) {

        return substr($name, 0, $max) . '...';
    } else {
        return $name;
    }
}

function enviarEmail($nomeRemetente, $emailRemetente, $emaildestinatario, $assunto, $mensagem) {

    /* Verifica qual é o sistema operacional do servidor para ajustar o cabeçalho de forma correta. Não alterar */
    if (PHP_OS == "Linux")
        $quebra_linha = "\n"; //Se for Linux
    elseif (PHP_OS == "WINNT")
        $quebra_linha = "\r\n"; // Se for Windows
    else
        die("Este script nao esta preparado para funcionar com o sistema operacional de seu servidor");


    /* Montando o cabeçalho da mensagem */
    $headers = "MIME-Version: 1.1" . $quebra_linha;
    $headers .= "Content-type: text/html; charset=utf-8" . $quebra_linha;

    // Perceba que a linha acima contém "text/html", sem essa linha, a mensagem não chegará formatada.
    $headers .= "From: $nomeRemetente <" . $emailRemetente . ">" . $quebra_linha;
    $headers .= "Return-Path: " . $emailRemetente . $quebra_linha;

    /* Enviando a mensagem */
    $enviado = mail($emaildestinatario, $assunto, $mensagem, $headers, "-r" . $emailRemetente);

    return $enviado;
}

// Converte "AAAA-MM-DD" => "DDMMAAAA", retorna null se não converter
function printDate($date) {

    try {

        $date = substr($date, 0, 10);


        list($year, $month, $day) = explode("-", $date);
        $dateConvert = $day . $month . $year;

        $r = $dateConvert;

        return $r;
    } catch (Exception $e) {

        return null;
    }
}

function issetor(&$var, $default = false) {
    return isset($var) ? $var : $default;
}

// Converte "AAAA-MM-DD" => "DD/MM/AAAA" e  "AAAA-MM-DD HH:MM:SS" => "DD/MM/AAAA HH:MM:SS"
function fixDate($dataTime, $time = true) {

    try {

        if (!$time) {
            $dataTime = substr($dataTime, 0, 10);
        } else {
            $dataTime = substr($dataTime, 0, 19);
        }

        $dataTime = trim($dataTime);

        @list($date, $time) = explode(" ", $dataTime);

        if (isset($date) && (strpos($date, '-') !== false)) {
            list($year, $month, $day) = explode("-", $date);

            $date = "$day/$month/$year";
        }
    } catch (Exception $e) {
        return null;
    }

    $retorno = isset($time) ? $date . ' ' . $time : $date;

    return $retorno;
}

function fixOnlyDate($dataTime) {
    return fixDate($dataTime, false);
}

function dateToW3C($data) {

    return substr($data, 0, 10);
}

// Converte DD/MM/AAAA" => "AAAA-MM-DD", retorna null se não converter
function brDateToW3C($data) {


    try {

        if (strlen($data) == 10) {
            list($day, $month, $year) = explode("/", $data);
            $date = $year . '-' . $month . '-' . $day;
            if (checkdate($month, $day, $year)) {
                return $date;
            }
        }
    } catch (Exception $e) {
        return null;
    }

    return null;
}

function printBoolenPhp($b) {

    return ($b) ? 'true' : 'false';
}

function select($key, $test) {

    echo selectPhp($key, $test);
}

function selectPhp($key, $test) {

    if (isset($test)) {

        $key2 = $key === 'true' || $key === 1 || $key === true ? '1' : $key;
        $key2 = $key === 'false' || $key === 0 || $key === false ? '0' : $key2;

        $test2 = $test === 'true' || $test === 1 || $test === true ? '1' : $test;
        $test2 = $test === 'false' || $test === 0 || $test === false ? '0' : $test2;

        if ($test2 == $key2) {

            return 'selected="selected"';
        } else {
            
        }
    }

    return "";
}

function checked($key) {

    if (isset($key) && ($key === true || $key === 'true' || $key === 1 || $key === '1')) {

        return 'checked="checked"';
    } else {
        return false;
    }
}

/**
 * 
 * @global type $error_messages
 * @param type $id
 * @param type $name
 * @param type $exibe
 * @return type
 */
function labelErro($id, $name = false, $exibe = false) {

    global $error_messages;

    $name = $name == false ? $id : $name;

    $html = "";
    $display = "style=\"display: none\"";

    if (isset($error_messages[$id]) || ($exibe)) {
        $display = "";
    }
    #if (isset($error_messages[$id]) || ($b)) {

    $s = isset($error_messages[$id]) ? $error_messages[$id] : '';
    $html = "<label $display id=\"erro_$id\"  for=\"$name\" generated=\"true\" class=\"error\">$s</label>";
    #}



    return $html;
}

function converterImagemParaJPG($tipoArquivo, $imagem_entrada, $imagem_saida, $config) {

    $img = WideImage::load($imagem_entrada . '.' . $tipoArquivo);

    $width = $img->getWidth();
    $height = $img->getHeight();

    if (isset($config['resize-max-width']) && isset($config['resize-max-height'])) {

        if ($img->getWidth() > $config['resize-max-width'] || $img->getHeight() > $config['resize-max-height']) {
            #deve 'caber' dentro dessa dimensão
            $img = $img->resize($config['resize-max-width'], $config['resize-max-height'], 'inside');
        }
    }


    if (isset($config['resize-min-width'])) {
        if ($img->getWidth() < $config['resize-min-width']) {
            $img = $img->resize($config['resize-min-width'], $img->getHeight(), 'fill');
        }
    }

    if (isset($config['resize-min-height'])) {
        if ($img->getHeight() < $config['resize-min-height']) {
            $img = $img->resize($img->getWidth(), $config['resize-min-height'], 'fill');
        }
    }



    $img->saveToFile($imagem_saida . '.jpg');

    $img_new = WideImage::load($imagem_saida . '.jpg');

    if ($tipoArquivo == 'jpg' && $width == $img_new->getWidth() && $height == $img_new->getHeight() && filesize($imagem_saida . '.jpg') > filesize($imagem_entrada . '.' . $tipoArquivo)) {

        unlink($imagem_saida . '.jpg');

        rename($imagem_entrada . '.' . $tipoArquivo, $imagem_saida . '.jpg');
    } else {
        unlink($imagem_entrada . '.' . $tipoArquivo);
    }
}

function croppImage($pasta, $nome, $newName, $width, $heigth, $force = false) {

    $array = array();

    $tipo = 'jpg';

    $file1 = $pasta . $nome . '.' . $tipo;
    $file2 = $pasta . $newName . '.' . $tipo;


    $img = WideImage::load($file1);

    if ($force || ($img->getWidth() > $width || $img->getHeight() > $heigth)) {

        $cropped_m = $img->resize($width, $heigth, 'inside');
    } else {
        $cropped_m = $img;
    }

    $cropped_m->saveToFile($file2, 85);

    $array['width'] = $cropped_m->getWidth();
    $array['height'] = $cropped_m->getHeight();
    $array['file'] = $file2;


    return $array;
}

/**
 * 
 * 
 * resize-min-width => redimensiona a imagem caso não tenha a largura mínima passada nesse parametro
 * resize-min-height => redimensiona a imagem caso não tenha a altura  mínima passada nesse parametro
 * 
 * resize-max-width => redimensiona a imagem caso ultrapasse a largura passada nesse parametro
 * resize-max-height => redimensiona a imagem caso ultrapasse a altura passada nesse parametro
 * 
 * max-bytes = tamnho máximo em bytes da imagem , caso não setado padrão 1048576 ( 1 M ) 
 * type = tipos de arquivos aceitos, se falso, todos os arquivos, se não definido, somente arquivos de imagens
 * 
 */
function uploadFile($arquivo, $pasta, $nome, $config) {


    $size = isset($config['max-bytes']) ? $config['max-bytes'] : 1048576; #1024 * 1024

    $retorno = array();
    $retorno['success'] = false;

    if (isset($arquivo)) {

        if (!empty($arquivo["tmp_name"])) {

            $tamanhos = getimagesize($arquivo["tmp_name"]);


            $tipoArquivo = strtolower(getTypeFile($arquivo));

            if (!in_array($tipoArquivo, array("png", "bmp", "jpg", "gif", "jpeg"))) {
                $retorno["erro"] = "Tipo de arquivo não é uma imagem válida. Tipos suportados: PNG, BMP, JPG e GIF";
            } else if ($arquivo["size"] > $size) {
                $retorno["erro"] = "Esta imagem excede o limite de 1MB. Tente novamente com outra imagem";
            } else if (($tamanhos[0] < 0 || $tamanhos[1] < 0)) {

                $retorno["erro"] = "A imagem precisa ter no mínimo 50 pixels. Tente novamente com uma imagem maior";
            } else {

                if (move_uploaded_file($arquivo['tmp_name'], $pasta . $nome . '2.' . $tipoArquivo)) {

                    $retorno['success'] = true;

                    converterImagemParaJPG($tipoArquivo, $pasta . $nome . '2', $pasta . $nome, $config);
                } else {
                    $retorno["erro"] = "Erro ao fazer upload";
                }
            }
        } else {
            $retorno["erro"] = "Não foi possivel obter o nome do arquivo";
        }
    } else {
        $retorno["erro"] = "Arquivo nao setado";
    }
    return $retorno;
}

function getTypeFile($arquivo) {

    $infos = explode(".", $arquivo["name"]);
    $tipoArquivo = $infos[count($infos) - 1];

    return $tipoArquivo;
}

function printMoney($valor, $testNull = false) {
    if ($testNull && empty($valor))
        return '';
    else
        return number_format($valor, 2, ',', '.');
}

function mask($mascara, $string) {
    $string = str_replace(" ", "", $string);
    for ($i = 0; $i < strlen($string); $i++) {
        $mascara[strpos($mascara, "#")] = $string[$i];
    }
    return $mascara;
}

function normatiza($string) {


    $string = str_replace('\'', '_', $string);

    $string = pg_escape_string(trim($string)); #necessário?
    $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ:&*| ';
    $b = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYobaaaaaaaceeeeiiiidnoooooouuuyybyRr_____';
    $string = utf8_decode($string);
    $string = strtr($string, utf8_decode($a), $b); //substitui letras acentuadas por "normais"
    $r = utf8_encode($string); //finaliza, gerando uma saída para a funcao

    return $r;
}

function format_link($string) {

    return preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.%-]*(\?\S+)?)?)?)@', '<a class="link" href="$1" target="_blank">$1</a>', $string);
}

function out($break, $string, $strip = true) {

    if ($strip == true) {

        if ($break) {
            #permite quebra de linha pela tag br
            $string = strip_tags($string, '<br>');
            #troca \n por <br>
            $string = preg_replace("/(\\r)?\\n/i", "<br/>", $string);
        } else {

            $string = strip_tags($string);
        }
    } else {

        $string = htmlspecialchars($string);
        if ($break) {
            #permite quebra de linha pela tag br          
            #troca \n por <br>
            $string = preg_replace("/(\\r)?\\n/i", "<br/>", $string);
        }
    }

    return $string;
}

function outl($break, $string, $strip = true) {

    $string = format_link(out($break, $string, $strip));

    return $string;
}

function outBreak($string) {

    #troca \n por <br>
    $string = preg_replace("/(\\r)?\\n/i", "<br/>", $string);
    return $string;
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE;
}

/* -----------------------------------------------------------------------------
 * Function: mobilize( $buffer )
 * Purpose:  Compresses HTML for mobile delivery
 * --------------------------------------------------------------------------- */

function _mobilize($buffer) {
    $chunks = preg_split('/(<script.*?\/script>)/ms', $buffer, -1, PREG_SPLIT_DELIM_CAPTURE);
    $buffer = '';
    foreach ($chunks as $c) {
        if (strpos($c, '<script') !== 0) {

            $c = preg_replace('/\s+/', ' ', $c);
        } else {
            $c = slib_compress_script($c);
        }
        $buffer .= $c;
    }
    return $buffer;
}

function _slib_compress_script($buffer) {

    // JavaScript compressor by John Elliot <jj5@jj5.net>

    $replace = array(
        '#\'([^\n\']*?)/\*([^\n\']*)\'#' => "'\1/'+\'\'+'*\2'", // remove comments from ' strings
        '#\"([^\n\"]*?)/\*([^\n\"]*)\"#' => '"\1/"+\'\'+"*\2"', // remove comments from " strings
        '#/\*.*?\*/#s' => "", // strip C style comments
        '#[\r\n]+#' => "\n", // remove blank lines and \r's
        '#\n([ \t]*//.*?\n)*#s' => "\n", // strip line comments (whole line only)
        '#([^\\])//([^\'"\n]*)\n#s' => "\\1\n",
        // strip line comments
        // (that aren't possibly in strings or regex's)
        '#\n\s+#' => "\n", // strip excess whitespace
        '#\s+\n#' => "\n", // strip excess whitespace
        '#(//[^\n]*\n)#s' => "\\1\n", // extra line feed after any comments left
        // (important given later replacements)
        '#/([\'"])\+\'\'\+([\'"])\*#' => "/*" // restore comments in strings
    );

    $search = array_keys($replace);
    $script = preg_replace($search, $replace, $buffer);

    $replace = array(
        "&&\n" => "&&",
        "||\n" => "||",
        "(\n" => "(",
        ")\n" => ")",
        "[\n" => "[",
        "]\n" => "]",
        "+\n" => "+",
        ",\n" => ",",
        "?\n" => "?",
        ":\n" => ":",
        ";\n" => ";",
        "{\n" => "{",
//  "}\n"  => "}", (because I forget to put semicolons after function assignments)
        "\n]" => "]",
        "\n)" => ")",
        "\n}" => "}",
        "\n\n" => "\n"
    );

    $search = array_keys($replace);
    $script = str_replace($search, $replace, $script);

    return trim($script);
}

#inicio modulo de paginação-------------------------------------------------------------------------------------------------------------------



global $paginator;
$paginator = new stdClass();
$paginator->offset = 0;
$paginator->is_pagination = false;
$paginator->count = 0;
$paginator->page = 1;
$paginator->qt_paginas = false;
$paginator->query_string_paginator = "";
$paginator->url = false;
$paginator->start = false;
$paginator->end = false;

$paginator->count = filter_input(INPUT_GET, "countr", FILTER_VALIDATE_INT);
if ($paginator->count) {


    $paginator->is_pagination = true;
}

function setMaxResult($max) {

    global $paginator;

    $paginator->max = $max;

    //calacular o offset;
    $paginator->page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
    if ($paginator->page) {
        $paginator->offset = ($paginator->max * $paginator->page) - $paginator->max;

        if ($paginator->offset < 0) {
            $paginator->offset = 0;
        }
    } else {
        $paginator->page = 1;
    }
}

function print_prev($class = 'prev', $text = '« Anterior') {

    global $paginator;

    $i = $paginator->page - 1;
    if ($paginator->page != 1) {
        echo "<a class='$class' " . print_paginator($i) . " >$text</a>";
    }
}

function print_next($class = 'next', $text = 'Próximo »') {

    global $paginator;

    $i = $paginator->page + 1;
    if ($paginator->page != $paginator->qt_paginas) {
        echo "<a class='$class' " . print_paginator($i) . "  >$text</a>";
    }
}

function print_paginator($i, $func = 'ajaxpagination') {



    global $paginator;

    //..
    $merge = array_merge($_GET, $_POST);
    $paginator->query_string_paginator = "";
    foreach ($merge as $chave => $valor) {

        if (is_array($valor)) {
            foreach ($valor as  $a) {
                $paginator->query_string_paginator .= "&" . urlencode($chave) . "[]=" . urlencode($a);
            }
        } else {
            if ($chave != 'page' && $chave != 'qtp')
                $paginator->query_string_paginator .= "&" . urlencode($chave) . "=" . urlencode($valor);
        }
    }

    //..

    if ($paginator->page == $i) {
        $return = " class='active' ";
    } else {

        $u = $paginator->query_string_paginator . "&page=" . $i . '&qtp=' . $paginator->qt_paginas;

        if (!$paginator->is_pagination) {
            $u .= "&countr=" . $paginator->count;
        }

        if ($paginator->url !== false) {

            if (empty($paginator->url)) {
                $src = '';
            } else {
                $src = _src($paginator->url);
            }

            $return = "href=\"" . $src . "?" . $u . "\"";
        } else {

            $script = $func . "('" . $u . "')";

            $return = "href=\"#.;\"  onclick=\"$script\"  ";
        }
    }


    return $return;
}

function calc_paginator() {

    global $paginator;


    if (isset($paginator->max)) {

        $paginator->qt_paginas = ceil($paginator->count / $paginator->max);
    }


    $paginator->start = $paginator->page - 5 > 1 ? $paginator->page - 5 : 1;
    $paginator->end = $paginator->page + 5 < $paginator->qt_paginas ? $paginator->page + 5 : $paginator->qt_paginas;
}

#fim modulo de paginação-------------------------------------------------------------------------------------------------------------------
