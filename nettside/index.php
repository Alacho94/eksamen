<?php
    require 'core/init.php';

    $filter = "";
    $kapasitet = 0;
    $projektor = '';
    $dato = dagensDato();

    if (isset($_GET['personer'])) {
        $kapasitet = $_GET['personer'];
        
        if (empty($filter)) $filter = " WHERE";

        if ($kapasitet < 2 || $kapasitet > 4) {
            $filter .= " kapasitet > 0";
        } else {
            $filter .= " kapasitet = '$kapasitet'";
        }
    }

    if (isset($_GET['projektor'])) {
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

    if (isset($_GET['dato'])) {
        $dato = $_GET['dato'];
    }
    
    require 'core/header.php';
?>
    <h1>Velkommen</h1>
    <form method="get" action="index.php">
        <select name="personer">
            <option <?php if ($kapasitet < 2 || $kapasitet > 4) echo 'selected="selected"'; ?> disabled="disabled" value="">Velg kapasitet</option>
            <option <?php if ($kapasitet == 2) echo 'selected="selected"'; ?> value="2">2 personer</option>
            <option <?php if ($kapasitet == 3) echo 'selected="selected"'; ?> value="3">3 personer</option>
            <option <?php if ($kapasitet == 4) echo 'selected="selected"'; ?> value="4">4 personer</option>
        </select>
        /
        <select name="projektor">
            <option <?php if (empty($projektor)) echo 'selected="selected"'; ?> disabled="disabled" value="">Velg projektor</option>
            <option <?php if ($projektor == 'ja') echo 'selected="selected"'; ?> value="ja">Ja</option>
            <option <?php if ($projektor == 'nei') echo 'selected="selected"'; ?> value="nei">Nei</option>
        </select>
        /
        <input type="text" name="dato" id="dato" placeholder="Dato (dd.mm.åååå)" value="<?php echo $dato; ?>">
        /
        <input type="submit" value="Finn rom">
    </form>
<?php
    $dato = date('Y-m-d', strtotime($dato));

    $query = "SELECT * FROM rom";
    $query .= $filter;
    $sql = $database->prepare("$query;");
    $sql->setFetchMode(PDO::FETCH_OBJ);
    $sql->execute();

    while ($element = $sql->fetch()) {
        echo '<div class="rom"><div class="romDetaljer">RomNr: ' . $element->romNr . ', Kapasitet: ' . $element->kapasitet . ', Projektor: ' . harProjektor($element->projektor) . '</div><div class="romTimer">';
        
        echo '<form action="bookRom.php" method="post">';
        
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
                echo '<span class="opptatt"><input name="timer[]" disabled="disabled" value="' . $tid->timeID . '" type="checkbox">' . timesFormatering($tid->fraTid) . ' - ' . timesFormatering($tid->tilTid) . '</span><br />';
            } else {
                echo '<span class="ledig"><input name="timer[]" value="' . $tid->timeID . '" type="checkbox">' . timesFormatering($tid->fraTid) . ' - ' . timesFormatering($tid->tilTid) . '</span><br />';
            }
            
        }
        
        echo '<input type="hidden" name="romnr" value="' . $element->romNr . '">';
        echo '<input type="hidden" name="dato" value="' . $dato . '">';
        echo '<input type="text" name="brukernavn" placeholder="Brukernavn" maxlength="10">';
        echo '<input type="password" name="passord" placeholder="Passord">';
        echo '<br />';
        echo '<input type="submit" value="Hold av rom">';
        echo '</form>';
        
        echo '</div></div>';
    }
    require 'core/footer.php';
?>