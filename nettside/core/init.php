<?php
    require 'database/db.php';
    
    // En funksjon som gjør om verdien fra tall til ja/nei.
    function harProjektor($verdi) {
        if ($verdi == 0) {
            return 'Nei';   
        } else {
            return 'Ja';
        }
    }
    
    // Meget enkel funksjon som gir oss datoen i forventet format.
    function dagensDato() {
        return date("d.m.Y");
    }
    
    // Fjerner sekunder fra visning av klokka (formatering).
    function timesFormatering($tiden) {
        return date('H:i', strtotime($tiden));
    }

    function datoFormatering($dato) {
        return date('d.m.Y', strtotime($dato));
    }
    
    // En funksjon som skjekker om datoen er en faktisk dato og at den matcher vår formatering.
    function validDato($dato, $format = 'd.m.Y') {
        $resultat = DateTime::createFromFormat($format, $dato);
        return $resultat && $resultat->format($format) == $dato;
    }

    // Denne funksjonen sjekker om rommet er ledig den gitte timen, den gitte datoen.
    function ledigRom($romNr, $dato, $timeID) {
        global $database;
        
        $sql = $database->prepare("
            SELECT COUNT(*) FROM booking
            JOIN bookingTimer ON bookingTimer.bookingID = booking.bookingID
            WHERE booking.romNr = '$romNr' AND booking.dato = '$dato' AND bookingTimer.timeID = '$timeID';
        ");
        $sql->execute();
        
        if ($sql->fetchColumn()) {
            return true;
        } else {
            return false;
        }
    }
    
    // En enkel funksjon som sjekker om bruker eksisterer og at passord er riktig.
    function brukerFinnes($brukernavn, $passord) {
        global $database;
        
        // Krypterer passordet for å matche hash-krypteringen i databasen.
        $passord = md5($passord);
        
        $sql = $database->prepare("
            SELECT COUNT(*) FROM brukere
            WHERE brukernavn = '$brukernavn' AND passord = '$passord';
        ");
        $sql->execute();

        if ($sql->fetchColumn()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Denne funksjonen henter ut brukernavn fra bruker-ID
    function idFraBrukernavn($brukernavn) {
        global $database;
        
        $sql = $database->prepare("SELECT brukerID FROM brukere WHERE brukernavn = :brukernavn;");
        $sql->bindParam(':brukernavn', $brukernavn, PDO::PARAM_STR);
        $sql->execute();
        $id = $sql->fetchColumn();
        
        return $id;
    }
    
    // Denne funksjonen henter ut brukerens rettigheter ut i fra brukernavn
    function typeFraBrukernavn($brukernavn) {
        global $database;
        
        $sql = $database->prepare("SELECT rettigheter FROM brukere WHERE brukernavn = :brukernavn;");
        $sql->bindParam(':brukernavn', $brukernavn, PDO::PARAM_STR);
        $sql->execute();
        $type = $sql->fetchColumn();
        
        return $type;
    }
    
    // Denne funksjonen skriver ut errorer, ut i fra en php array.
    function printError($error) {
        return '<ul class="error"><li>' . implode('</li><li>', $error) . '</li></ul>';
    }
    
    // En annen error funksjon som brukes i bookRom.php for å sende error til hjem.php
    function error($msg, $dato, $romNr) {
		$error = $msg;
        $dato = datoFormatering($dato);
		header("Location: hjem?error=$error&dato=$dato#rom$romNr");
		exit;
	}

    // En funksjon som teller hvor mange timer som eksisterer totalt på hvert rom.
    function totalTimer() {
        global $database;
        
        $sql = $database->prepare("SELECT COUNT(*) FROM timer");
        $sql->execute();
        $verdi = $sql->fetchColumn();
        
        return $verdi;
    }
    
    // En funksjon som teller hvor mange timer som er registrert på et rom på en dag.
    function antallReserverteTimer($romNr, $dato) {
        global $database;
        
        $dato = date('Y-m-d', strtotime($dato));
        
        $sql = $database->prepare("
            SELECT COUNT(*) FROM bookingTimer
            JOIN booking ON booking.bookingID = bookingTimer.bookingID
            WHERE booking.romNr = :romNr AND booking.dato = :dato;
        ");
        $sql->bindParam(':romNr', $romNr, PDO::PARAM_INT);
        $sql->bindParam(':dato', $dato, PDO::PARAM_STR);
        $sql->execute();
        $verdi = $sql->fetchColumn();
        
        return $verdi;
    }
?>