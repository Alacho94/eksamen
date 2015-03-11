<?php
    require 'core/init.php';

    // Tabell 1 start
    $sql = $database->prepare("
        CREATE TABLE rom (
        romNr INT(4) NOT NULL UNIQUE,
        kapasitet INT(1) NOT NULL,
        projektor INT(1) NOT NULL,
        PRIMARY KEY (romNr));
    ");
    $sql->execute() or die('Noe gikk galt. Tabell 1 kunne ikke opprettes, prosessen stoppes.');

    $sql = $database->prepare("
        INSERT INTO rom VALUES 
        (101, 2, 0),
        (102, 2, 1),
        (103, 3, 0),
        (104, 3, 1),
        (105, 3, 1),
        (106, 4, 0),
        (107, 4, 0),
        (108, 4, 1),
        (109, 4, 1);
    ");
    $sql->execute() or die('Noe gikk galt ved tabell 1, slett tabellen "rom" og start på nytt.');
    // Tabell 1 slutt

    // Tabell 2 start
    $sql = $database->prepare("
        CREATE TABLE brukere (
        brukerID INT(5) NOT NULL AUTO_INCREMENT,
        brukernavn VARCHAR(10) NOT NULL UNIQUE,
        passord VARCHAR(32) NOT NULL,
        rettigheter INT(1) NOT NULL DEFAULT 0,
        PRIMARY KEY (brukerID));
    ");
    $sql->execute() or die('Noe gikk galt. Tabell 2 kunne ikke opprettes, prosessen stoppes. (PS. slett tabellen "rom" før du prøver på nytt).');
    
    $sql = $database->prepare("
        INSERT INTO brukere (brukernavn, passord, rettigheter) VALUES
        ('bruker1', '" . md5('passord') . "', 0),
        ('bruker2', '" . md5(123456) . "', 0),
        ('admin', '" . md5('adminPassord') . "', 1);
    ");
    $sql->execute() or die('Noe gikk galt ved tabell 1, slett tabellene "rom" og "brukere", for så å starte på nytt.');
    // Tabell 2 slutt
    
    // Tabell 3 start
    $sql = $database->prepare("CREATE TABLE booking (
        bookingID INT(5) NOT NULL AUTO_INCREMENT,
        brukerID INT(5) NOT NULL,
        romNr INT(4) NOT NULL,
        dato DATE NOT NULL,
        PRIMARY KEY (bookingID),
        FOREIGN KEY (brukerID) REFERENCES brukere(brukerID),
        FOREIGN KEY (romNr) REFERENCES rom(romNr));
    ");
    $sql->execute() or die('Noe gikk galt. Tabell 3 kunne ikke opprettes, prosessen stoppes. (PS. slett tabellene "rom" og "brukere" før du prøver på nytt).');
    // Tabell 3 slutt

    // Tabell 4 start
    $sql = $database->prepare("CREATE TABLE timer (
        timeID INT(1) NOT NULL,
        fraTid TIME NOT NULL,
        tilTid TIME NOT NULL,
        PRIMARY KEY (timeID));
    ");
    $sql->execute() or die('Noe gikk galt. Tabell 4 kunne ikke opprettes, prosessen stoppes. (PS. slett tabellene "rom", "brukere" og "booking" før du prøver på nytt).');

    $sql = $database->prepare("
        INSERT INTO timer (timeID, fraTid, tilTid) VALUES
        (1, '08:00:00', '09:00:00'),
        (2, '09:00:00', '10:00:00'),
        (3, '10:00:00', '11:00:00'),
        (4, '11:00:00', '12:00:00'),
        (5, '12:00:00', '13:00:00'),
        (6, '13:00:00', '14:00:00'),
        (7, '14:00:00', '15:00:00'),
        (8, '15:00:00', '16:00:00'),
        (9, '16:00:00', '17:00:00');
    ");
    $sql->execute() or die('Noe gikk galt ved tabell 4, slett tabellene "rom", "brukere", "booking" og "timer" for så å starte på nytt.');
    // Tabell 4 slutt

    // Tabell 5 start
    $sql = $database->prepare("CREATE TABLE bookingTimer (
        bookingID INT(5) NOT NULL,
        timeID INT(1) NOT NULL,
        PRIMARY KEY (bookingID, timeID),
        FOREIGN KEY (bookingID) REFERENCES booking(bookingID),
        FOREIGN KEY (timeID) REFERENCES timer(timeID));
    ");
    $sql->execute() or die('Noe gikk galt. Tabell 5 kunne ikke opprettes, prosessen stoppes. (PS. slett tabellene "rom", "brukere", "booking" og "timer" før du prøver på nytt).');
    // Tabell 5 slutt
    
    echo 'Nødvendige tabeller ble opprettet i databasen.';
?>