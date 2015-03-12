<?php
    $tittel = 'Book rom';
    require 'core/init.php';

    $filter = "";
    $kapasitet = 0;
    $projektor = '';
    $dato = dagensDato();

    if (isset($_GET['personer']) && !empty($_GET['personer'])) {
        $kapasitet = $_GET['personer'];
        
        if (empty($filter)) $filter = " WHERE";

        if ($kapasitet < 2 || $kapasitet > 4) {
            $filter .= " kapasitet > 0";
        } else {
            $filter .= " kapasitet = '$kapasitet'";
        }
    }

    if (isset($_GET['projektor']) && !empty($_GET['projektor'])) {
        $projektor = $_GET['projektor'];
        
        if (empty($filter)) {
            $filter = " WHERE";
        } else {
            $filter .= " AND";
        }
            
        if ($projektor == 'ja') {
            $filter .= " projektor = 1";
        } else {
            $filter .= " projektor = 0";
        }
    }

    if (isset($_GET['dato']) && !empty($_GET['dato'])) {
        if (validDato($_GET['dato'])) {
            $dato = $_GET['dato'];
        } else {
            $error[] = 'Datoen du har oppgitt er ugyldig, datoen blir rettet til dagens dato.';
        }
    }

    require 'core/header.php';
    if (isset($_GET['suksess']) && empty($_GET['suksess'])) {
        echo '<script type="text/javascript">alert("Suksess, du har booket et rom.");</script>';
    } else if (isset($_GET['error']) && !empty($_GET['error'])) {
        echo '<script type="text/javascript">alert("' . $_GET['error'] . '");</script>';
    }
?>
    <h1>Westerdals rombooking</h1>
    <p>Velkommen til Westerdals Oslo ACTs tjeneste for å booke grupperom.</p>
    <?php if (isset($error)) print_r($error); ?>
    <form id="filter" method="get" action="index.php">
        <select name="personer">
            <option <?php if ($kapasitet < 2 || $kapasitet > 4) echo 'selected="selected"'; ?> disabled="disabled" value="">Velg kapasitet</option>
            <option <?php if ($kapasitet == 2) echo 'selected="selected"'; ?> value="2">2 personer</option>
            <option <?php if ($kapasitet == 3) echo 'selected="selected"'; ?> value="3">3 personer</option>
            <option <?php if ($kapasitet == 4) echo 'selected="selected"'; ?> value="4">4 personer</option>
        </select>
        <select name="projektor">
            <option <?php if (empty($projektor)) echo 'selected="selected"'; ?> disabled="disabled" value="">Velg projektor</option>
            <option <?php if ($projektor == 'ja') echo 'selected="selected"'; ?> value="ja">Ja</option>
            <option <?php if ($projektor == 'nei') echo 'selected="selected"'; ?> value="nei">Nei</option>
        </select>
        <input type="text" name="dato" id="dato" placeholder="Dato (dd.mm.åååå)" value="<?php echo $dato; ?>">
        <input type="submit" value="Finn rom">
    </form>
    <div id="romOversikt">
<?php
    $dato = date('Y-m-d', strtotime($dato));

    $query = "SELECT * FROM rom";
    $query .= $filter;
    $sql = $database->prepare("$query;");
    $sql->setFetchMode(PDO::FETCH_OBJ);
    $sql->execute();

    while ($element = $sql->fetch()) {
        echo '
            <div class="rom inaktiv">
                <div class="romDetaljer">RomNr: ' . $element->romNr . ', Kapasitet: ' . $element->kapasitet . ', Projektor: ' . harProjektor($element->projektor) . '
                </div>
                <div class="romTimer">
        ';
        
        echo '<form id="timesBestilling" action="bookRom.php" method="post"><div class="checks">';
        
        $sql2 = $database->prepare("SELECT * FROM timer");
        $sql2->setFetchMode(PDO::FETCH_OBJ);
        $sql2->execute();
        
        while ($tid = $sql2->fetch()) {
        
            $rom = $database->prepare("
                SELECT COUNT(*) FROM booking
                JOIN bookingTimer ON bookingTimer.bookingID = booking.bookingID
                WHERE booking.romNr = '$element->romNr' AND booking.dato = '$dato' AND bookingTimer.timeID = '$tid->timeID';
            ");
            $rom->execute();
            if ($rom->fetchColumn()) {
                echo '<div class="timeCheck opptatt"><input id="' . $element->romNr . $tid->timeID . '" name="timer[]" disabled="disabled" value="' . $tid->timeID . '" type="checkbox"><label for="'. $element->romNr . $tid->timeID . '">' . timesFormatering($tid->fraTid) . ' - ' . timesFormatering($tid->tilTid) . '</label></div>';
            } else {
                echo '<div class="timeCheck ledig"><input id="' . $element->romNr . $tid->timeID . '" name="timer[]" value="' . $tid->timeID . '" type="checkbox"><label for="' . $element->romNr . $tid->timeID . '">' . timesFormatering($tid->fraTid) . ' - ' . timesFormatering($tid->tilTid) . '</label></div>';
            }
            
        }
        
        echo '</div>';
        echo '<input type="hidden" name="romnr" value="' . $element->romNr . '">';
        echo '<input type="hidden" name="dato" value="' . $dato . '">';
        echo '<input type="text" name="brukernavn" placeholder="Brukernavn" maxlength="10"><i class="fa fa-user brukerIkon"></i>';
        echo '<input type="password" name="passord" placeholder="Passord"><i class="fa fa-unlock-alt passordIkon"></i>';
        echo '<input type="submit" value="Hold av rom">';
        echo '</form>';
        
        echo '</div></div>';
    }
?>
    </div>
<?php
    require 'core/footer.php';
?>