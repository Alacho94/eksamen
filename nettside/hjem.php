<?php
    // Tittelen på siden, denne brukes i header.php
    $tittel = 'Book rom';
    
    // I denne filen ligger funksjoner som brukes, og databasetilkobling.
    require 'core/init.php';

    $filter = "";
    $kapasitet = 0;
    $projektor = '';
    $dato = dagensDato();
    
    // Ser etter filter for antall personer, og lager en fortsettelse på sql query.
    if (isset($_GET['personer']) && !empty($_GET['personer'])) {
        $kapasitet = $_GET['personer'];
        
        if (empty($filter)) $filter = " WHERE";

        if ($kapasitet < 2 || $kapasitet > 4) {
            $filter .= " kapasitet > 0";
        } else {
            $filter .= " kapasitet = '$kapasitet'";
        }
    }
    
    // Ser etter filter for projektor, og lager en fortsettelse på sql query.
    if (isset($_GET['projektor']) && !empty($_GET['projektor'])) {
        $projektor = $_GET['projektor'];
        
        if (empty($filter)) {
            $filter = " WHERE";
        } else {
            $filter .= " AND";
        }
            
        if ($projektor == 'ja') {
            $filter .= " projektor = 1";
        } else if ($projektor == "urelevant") {
            $filter .= " projektor >= 0";
        } else {
            $filter .= " projektor = 0";
        }
    }
    
    // Ser etter filter for dato, den brukes for å sjekke om rom er ledig, og for å sende dato videre til rombooking.
    if (isset($_GET['dato']) && !empty($_GET['dato'])) {
        if (validDato($_GET['dato'])) {
            $dato = $_GET['dato'];
        } else {
            $error[] = 'Datoen du har oppgitt er ugyldig, datoen blir rettet til dagens dato.';
        }
    }
    
    // Her importeres en header-fil med alt av css, js, og head-tag (start på html kode).
    require 'core/header.php';
    
    // Her ser vi etter statuser fra url'en. Dette brukes for å få fram meldinger på nettsiden (error/suksess).
    if (isset($_GET['suksess']) && empty($_GET['suksess'])) {
        echo '<script type="text/javascript">alert("Suksess, du har booket et rom.");</script>';
    } else if (isset($_GET['error']) && !empty($_GET['error'])) {
        echo '<script type="text/javascript">alert("' . $_GET['error'] . '");</script>';
    }
?>
    <h1>Westerdals rombooking</h1>
    <p>Velkommen til Westerdals Oslo ACTs tjeneste for å booke grupperom.</p>
    <?php if (isset($error)) print_r($error); ?>
    <form id="filter" method="get" action="hjem">
        <select name="personer">
            <option <?php if ($kapasitet < 2 || $kapasitet > 4) echo 'selected="selected"'; ?> disabled="disabled" value="">Velg kapasitet</option>
            <option <?php if ($kapasitet == 1) echo 'selected="selected"'; ?> value="1">Usikker</option>
            <option <?php if ($kapasitet == 2) echo 'selected="selected"'; ?> value="2">2 personer</option>
            <option <?php if ($kapasitet == 3) echo 'selected="selected"'; ?> value="3">3 personer</option>
            <option <?php if ($kapasitet == 4) echo 'selected="selected"'; ?> value="4">4 personer</option>
        </select>
        <select name="projektor">
            <option <?php if (empty($projektor)) echo 'selected="selected"'; ?> disabled="disabled" value="">Velg projektor</option>
            <option <?php if ($projektor == 'urelevant') echo 'selected="selected"'; ?> value="urelevant">Ikke relevant</option>
            <option <?php if ($projektor == 'ja') echo 'selected="selected"'; ?> value="ja">Ja</option>
            <option <?php if ($projektor == 'nei') echo 'selected="selected"'; ?> value="nei">Nei</option>
        </select>
        <input type="text" name="dato" id="dato" placeholder="Dato (dd.mm.åååå)" value="<?php echo $dato; ?>">
        <input type="submit" value="Finn rom">
    </form>
    <div id="romOversikt">
<?php
    $dato = date('Y-m-d', strtotime($dato));
    
    // Starter queryen.
    $query = "SELECT * FROM rom";
    // Legger til filter på queryen.
    $query .= $filter;
    $sql = $database->prepare("$query;");
    $sql->setFetchMode(PDO::FETCH_OBJ);
    $sql->execute();
    
    // Kjører en loop for hvert element i som PDO henter.
    while ($element = $sql->fetch()) {
        
        $romLedigeTimer = (totalTimer() - antallReserverteTimer($element->romNr, $dato));
        
        echo '
            <div id="rom' . $element->romNr . '" class="rom inaktiv">
                <div onclick="aapne(\'#rom' . $element->romNr.'\'); return false;" class="romDetaljer">
                    <span class="romRomNr"><i class="fa fa-map-marker"></i> ' . $element->romNr . '</span>
                    <span class="romKapasitet"><i class="fa fa-users"></i> ' . $element->kapasitet . '</span>
                    <span class="romProjektor"><i class="fa fa-video-camera"></i> ' . harProjektor($element->projektor) . '</span>
                    <span class="romStatus">' . $romLedigeTimer . ' ledige timer.</span>
                </div>
                <div class="romTimer">
        ';
        
        echo '<form id="timesBestilling" action="bookRom" method="post"><div class="checks">';
        
        // Her starter vi en ny database-kobling for å hente ut timer som man kan velge (da dette ligger i databasen).
        $sql2 = $database->prepare("SELECT * FROM timer;");
        $sql2->setFetchMode(PDO::FETCH_OBJ);
        $sql2->execute();
        
        while ($tid = $sql2->fetch()) {
            
            // Enda en ny database-kobling, denne sjekker om timen man skal velge er ledig/opptatt.
            // Disse tre database-koblingene, gir (utifra hvordan vi har forstått det) totalt en database-funksjon av php. Da disse tre sammen skaper en tabell som viser rom.
            
            // Her ser vi om querien som er laget stemmer. Hvis den utgir en rad, så vil rommet være opptatt på gjeldende time, ellers er det ledig.
            if (ledigRom($element->romNr, $dato, $tid->timeID)) {
                echo '<div class="timeCheck opptatt"><input id="' . $element->romNr . $tid->timeID . '" name="timer[]" disabled="disabled" value="' . $tid->timeID . '" type="checkbox"><label for="'. $element->romNr . $tid->timeID . '">' . timesFormatering($tid->fraTid) . ' - ' . timesFormatering($tid->tilTid) . '</label></div>';
            } else {
                echo '<div class="timeCheck ledig"><input id="' . $element->romNr . $tid->timeID . '" name="timer[]" value="' . $tid->timeID . '" type="checkbox"><label for="' . $element->romNr . $tid->timeID . '">' . timesFormatering($tid->fraTid) . ' - ' . timesFormatering($tid->tilTid) . '</label></div>';
            }
            
        }
        
        echo '</div>';
        echo '<input type="hidden" name="romnr" value="' . $element->romNr . '">';
        echo '<input type="hidden" name="dato" value="' . $dato . '">';
        echo '<input type="text" name="brukernavn" placeholder="Brukernavn" maxlength="10"><i class="fa fa-user brukerIkon"></i>';
        echo '<input type="password" name="passord" placeholder="Passord" maxlength="30"><i class="fa fa-unlock-alt passordIkon"></i>';
        echo '<input type="submit" value="Hold av rom">';
        echo '</form>';
        
        echo '</div></div>';
    }
?>
    </div>
<?php
    // Henter ut footer, bruker require for å slippe å skrive footer på nytt på hver side.
    require 'core/footer.php';
?>