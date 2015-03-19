<?php
    $tittel = 'Endre booking';
    require 'core/init.php';
    
    // Start på tabell-script
    if (isset($_POST['submit'])) {
        // Vi starter med å se hvilken submit button som er brukt.
        if ($_POST['submit'] == "Hent mine bookinger") {
            
            // Leter etter brukernavn og passord, evt sender tilbake feilmeldinger.
            if (empty($_POST['brukernavn'])) {
                $error[] = "Du må skrive inn et brukernavn.";   
            } else {
                $brukernavn = $_POST['brukernavn'];
            }
            if (empty($_POST['passord'])) {
                $error[] = "Du må skrive inn et passord.";   
            } else {
                $passord = $_POST['passord'];
            }
            
            // Hvis det ikke finnes noen feilmeldinger så vil den registrere rettigheter og evt. lage et filter.
            if (!isset($error)) {
                if (brukerFinnes($brukernavn, $passord)) {
                    $brukerID = idFraBrukernavn($brukernavn);
                    $type = typeFraBrukernavn($brukernavn);
                    
                    if ($type == 1) {
                        $filter = "";
                    } else {
                        $filter = "AND booking.brukerID = :brukerID";
                        
                    }
                } else {
                    $error[] = "Feil brukernavn eller passord.";
                }
            }
        } else if ($_POST['submit'] == "Fjern reservasjon") {
            if (!empty($_POST['booking']) && isset($_POST['booking'])) {
                $bookingID = $_POST['booking'];
                
                // Må kjøre to sql, queries da det skal fjernes fra to tabeller med foreign-key koblet sammen, det er her også avhengig hvilken rekkefølge som infoen slettes i.
                $sql = $database->prepare("DELETE FROM bookingTimer WHERE bookingID = :bookingID");
                $sql->bindParam(':bookingID', $bookingID, PDO::PARAM_INT);
                $sql->execute();
                
                $sql = $database->prepare("DELETE FROM booking WHERE bookingID = :bookingID");
                $sql->bindParam(':bookingID', $bookingID, PDO::PARAM_INT);
                $sql->execute();
                
                $suksess = 'fjernetBooking';
            } else {
                $error[] = "Noe gikk galt, og bookingen ble ikke fjernet.";
            }
        }
    }
    // Slutt på tabell-script

    require 'core/header.php';
    // Ser etter et suksess parameter i URL, og evt. gir en popup melding.
    if (isset($suksess) && $suksess == 'fjernetBooking') {
        echo '<script type="text/javascript">alert("Suksess, du har fjernet din booking.");</script>';
    }
?>
    <h1>Endre din booking</h1>
    <?php
    if (isset($brukerID) && !empty($brukerID)) {
        echo '<p>Hei, ' . $brukernavn . '. Her er din oversikt over bookede grupperom:</p>';
        if (isset($error)) echo printError($error);
        
        // En query som henter ut rom booking og info fra mange diverse tabeller som hører sammen.
        $sql = $database->prepare("
            SELECT booking.bookingID AS bookingID, brukere.brukernavn AS brukernavn, booking.romNr AS romNr, booking.dato AS dato, MIN(timer.fraTid) AS fraTid, MAX(timer.tilTid) AS tilTid, rom.kapasitet AS kapasitet, rom.projektor AS projektor FROM booking
            JOIN bookingTimer ON bookingTimer.bookingID = booking.bookingID
            JOIN timer ON timer.timeID = bookingTimer.timeID
            JOIN rom ON rom.romNr = booking.romNr
            JOIN brukere ON brukere.brukerID = booking.brukerID
            WHERE dato >= :dagensDato $filter
            GROUP BY booking.bookingID
            ORDER BY booking.dato ASC, timer.fraTid ASC
        ;");
        // legger til filter hvis det finnes
        if (!empty($filter)) $sql->bindParam(':brukerID', $brukerID, PDO::PARAM_INT);
        $enDato = date("Y-m-d");
        $sql->bindParam(':dagensDato', $enDato, PDO::PARAM_STR);
        $sql->setFetchMode(PDO::FETCH_OBJ);
        $sql->execute();
        
        // sjekker om noen rom finnes.
        if ($sql->rowCount() > 0) {
            echo '<div id="romOversikt">';
            while ($booking = $sql->fetch()) {
            ?>
                <div class="booketRom">
                    <?php if (empty($filter)) echo '<span class="bookingEier"><i class="fa fa-user"></i> ' . $booking->brukernavn . '</span>'; ?>
                    <span class="bookingDato"  title="Dato"><i class="fa fa-calendar-o"></i> <?php echo datoFormatering($booking->dato); ?></span>
                    <span class="booketRomNr" title="Rom nr."><i class="fa fa-map-marker"></i> <?php echo $booking->romNr; ?></span>
                    <span class="kapasitet" title="Kapasitet"><i class="fa fa-users"></i> <?php echo $booking->kapasitet; ?></span>
                    <span class="projektor" title="Projektor"><i class="fa fa-video-camera"></i> <?php echo harProjektor($booking->projektor); ?></span>
                    <span class="tidsRom" title="Tidsrom"><i class="fa fa-clock-o"></i> <?php echo timesFormatering($booking->fraTid) . ' - ' . timesFormatering($booking->tilTid); ?></span>
                    <form method="post" action="endreBooking">
                        <input type="hidden" value="<?php echo $booking->bookingID; ?>" name="booking" />
                        <input type="submit" name="submit" value="Fjern reservasjon" onclick="return avbestilling();" />
                    </form>
                </div>
            <?
            }
            echo '</div>';
        } else {
            echo '<span class="ingenResultater">Du har ikke booket noen rom enda.</span>';   
        }
    } else {
    ?>
        <p>Skriv inn ditt brukernavn og passord for å få en oversikt over hvilke rom du har booket, og kan fjerne.</p>
        <?php if (isset($error)) echo printError($error); ?>
        
        <!-- Login form for å få tilgang til riktige bookinger -->
        <form method="post" action="endreBooking" id="hentDinTabell">
            <input type="text" name="brukernavn" autofocus="autofocus" placeholder="Brukernavn" maxlength="10" <?php if (isset($brukernavn) && !empty($brukernavn)) echo 'value="' . $brukernavn . '"'; ?> />
            <input type="password" name="passord" placeholder="Passord" maxlength="30" />
            <input type="submit" name="submit" value="Hent mine bookinger" />
            <script src="js/placeholders.min.js"></script>
        </form>
    <?php
    }
    ?>
<?php
    require 'core/footer.php';
?>