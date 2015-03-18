<?php        
	require 'core/init.php';
    // Hent valgt dato og timenummer
    $dato = $_POST['dato'];
    $romNr = $_POST['romnr'];

    // Kontroller at dato er minst i dag og maks om 30 dager
    if ($dato < date("Y-m-d")) {
        error("Du kan ikke bestille tilbake i tid", $dato, $romNr);
    } else if ($dato > date("Y-m-d", strtotime("+30 days"))) {
        error("Du kan ikke bestille mer enn 30 dager fram i tid", $dato, $romNr);
    }

    // Sjekk om bruker har valgt minimum en time å holde av
    if (isset($_POST['timer']) && !empty($_POST['timer'])) {
        $timer = $_POST['timer'];
    } else {
        error("Du må velge minimum en time som du skal holde av.", $dato, $romNr);
    }
    
    // Sjekk om bruker har skrevet et brukernavn
    if(isset($_POST['brukernavn']) && !empty($_POST['brukernavn'])) {
        $brukernavn = $_POST['brukernavn'];
    } else {
        error("Du må skrive inn et brukernavn", $dato, $romNr);
    }
    
    // Sjekk om bruker har skrevet et passord
    if (isset($_POST['passord']) && !empty($_POST['passord'])) {
        $passord = $_POST['passord'];
        $passord = md5($passord);   // krypter passord med MD5
    } else {
        error("Du må skrive inn et passord", $dato, $romNr);
    }
    
	// Hent bruker ID og tilhørende passord fra database
    $sql = $database->prepare("SELECT `brukerID`, `passord` FROM `brukere` WHERE `brukernavn` = :brukernavn LIMIT 1");
    $sql->bindParam(':brukernavn', $brukernavn, PDO::PARAM_STR); // Bruk sanitize for å forhindre SQL-injection
	$sql -> execute();
	$user = $sql->fetch(PDO::FETCH_OBJ);
	$userID = $user->brukerID;
	$userPW = $user->passord;
    
	// Sjekk om valgt brukernavn eksisterer og passord er riktig før fortsettelse av bestilling
    if (isset($userID) && isset($passord) && $userPW == $passord) {

        // Lagre bestilling av rom i databasen på rom, bruker, dato
        $sql = $database->prepare("INSERT INTO `booking` (`brukerID`, `romNr`, `dato`) VALUES (:userID, :romNr, :dato)");
        $sql->bindParam(':userID', $userID, PDO::PARAM_STR);
        $sql->bindParam(':romNr', $romNr, PDO::PARAM_INT);
        $sql->bindParam(':dato', $dato, PDO::PARAM_STR);
        $sql->execute();

        // Hent booking id for bestillingen av rom
        $bookingID = $database->lastInsertId('bookingID');

        // Lagre bestilling av timene brukeren har valgt
        foreach ($timer as $timeID) {
            $sql = $database->prepare("INSERT INTO `bookingTimer` (`bookingID`, `timeID`) VALUES (:bookingID, :timeID)");
            $sql->bindParam(':bookingID', $bookingID, PDO::PARAM_INT);
            $sql->bindParam(':timeID', $timeID, PDO::PARAM_INT);
            $sql->execute();
        }

        // Bestilling er utført.
        header("Location: hjem?suksess");
    }
    
	// avbryt bestilling dersom brukernavn ikke eksisterer eller passord er feil
	else {
		error("Feil brukernavn/passord", $dato, $romNr);
	}
?>