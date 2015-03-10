<?php
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

    function romHoldtAv($timeID, $dato, $romNr) {
        $sql = $database->prepare("");
        
    }
?>