<?php
    require 'database/db.php';
    
    function harProjektor($verdi) {
        if ($verdi == 0) {
            return 'Nei';   
        } else {
            return 'Ja';
        }
    }

    function dagensDato() {
        return date("d.m.Y");
    }

    function timesFormatering($tiden) {
        return date('H:i', strtotime($tiden));
    }

    function validDato($dato, $format = 'd.m.Y') {
        $d = DateTime::createFromFormat($format, $dato);
        return $d && $d->format($format) == $dato;
    }
?>