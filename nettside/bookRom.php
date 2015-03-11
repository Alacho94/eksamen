<?php

	bookRom();

    function bookRom() {
    	require 'core/init.php';

    	// Hent valgt dato, bruker, passord, timer valgt
    	$dato = $_POST['dato'];
    	$romNr = $_POST['romnr'];
    	$timer = $_POST['timer'];
    	if(isset($_POST['brukernavn']) AND !empty($_POST['brukernavn'])) {
    		$brukernavn = $_POST['brukernavn'];
    	} else {
    		error("Du må skrive et brukernavn");
    	}
    	if(isset($_POST['passord']) AND !empty($_POST['passord'])) {
    		$passord = $_POST['passord'];
    		$passord = md5($passord);	// krypter passordet med MD5
    	} else {
    		error("Du må skrive et passord");
    	}

    	// Hent bruker ID og tilhørende passord fra database
	    $sql = $database->prepare("
	    	SELECT `brukerID`, `passord` FROM `brukere` WHERE `brukernavn` = '" . $brukernavn . "'");
		$sql -> execute();
		$user = $sql -> fetch(PDO::FETCH_OBJ);
		$userID = $user -> brukerID;
		$userPW = $user -> passord;
		$userPW = md5($passord);	// krypter passord med MD5


		// Sjekk om valgt brukernavn eksisterer og passord er riktig før fortsettelse av bestilling
		if (isset($userID) AND isset($passord) AND $userPW = $passord) {

			// Lagre bestilling av rom i databasen på rom, bruker, dato
		    $sql = $database->prepare("
		        INSERT INTO " . $db . ".`booking` (`brukerID`, `romNr`, `dato`) VALUES (" . $userID . "," . ($romNr) . ", '" . $dato . "') 
		    ");
		    $sql->execute();

		    // Hent booking id for bestillingen av rom
		    $bookingID = $database->lastInsertId('bookingID');

		    // Lagre bestilling av timene brukeren har valgt
			foreach ($timer as $timeID){
				$sql = $database->prepare("
		        	INSERT INTO " . $db . ".`bookingTimer` (`bookingID`, `timeID`) VALUES ( " . $bookingID . "," . $timeID . ") 
		   		");
		    $sql->execute();
			}

		    $success = "Du har fullført bestillingen";
		    header("Location: index.php?success");
		}

		// avbryt bestilling dersom brukernavn ikke eksisterer eller passord er feil
		else {
			error("Feil brukernavn/passord");
		}
	}

	function error($msg) {
		$error[] =  $msg;
		header("Location: index.php?error");
		exit;
	}
?>