<?php

define("validateDate_invalid", 'Preencha com uma data válida', true);
define("validateDate_minInvalid", 'Data inferior ao permitido', true);
define("validateDate_maxInvalid", 'Data superior ao permitido', true);

Class Validate {

    

    public function __construct() {
        
    }

    /**
     * Valida uma data
     * @param $value  data a ser validada no padrão YYYY-MM-DD
     * @param $minDate data mínima para ser comparada, poder ser passada em string (exemplo : 2000-01-01) ou em Objeto DateTime,se false não compara 
     * @param $maxDate data máxima para ser comparada, poder ser passada em string (exemplo : 2000-01-01) ou em Objeto DateTime,se false não compara 
     */
    public static function validateDate($name, $value, $dateMinParam = false, $dateMaxPram = false) {

        global $error_messages;

        $value = substr($value, 0, 10);


        $valid = true;

        $return = Array();
        $return['valid'] = false;
        $return['msg'] = '';
        #$return['error'] = false;

        if ($dateMinParam) {
            $dateMin = $dateMinParam instanceof DateTime ? $dateMinParam : new DateTime($dateMinParam);
        } else {
            $dateMin = false;
        }
        if ($dateMaxPram) {
            $dateMax = $dateMaxPram instanceof DateTime ? $dateMaxPram : new DateTime($dateMaxPram);
        } else {
            $dateMax = false;
        }


        @list($year, $month, $day) = explode("-", $value);
        if (!empty($day) && !empty($month) && !empty($year) && checkdate($month, $day, $year)) {

            $date = new DateTime("$year-$month-$day");

            #$dateMax = new DateTime();
            #$dateMin === false || $date > $dateMin
            if (($dateMax && $date > $dateMax)) {

                $valid = false;
                $return['msg'] = validateDate_maxInvalid;
            }
            if (($dateMin && $date < $dateMin)) {

                $valid = false;
                $return['msg'] = validateDate_minInvalid;
            }
        } else {

            $valid = false;
            $return['msg'] = validateDate_invalid;
        }

        $return['valid'] = $valid;

        if ($return['msg'] != '') {
            $error_messages[$name] = $return['msg'];
        }

        return $return;
    }

}
