<?php
    require 'core/init.php';

    if (isset($_GET['personer'])) {
        
    }
    require 'core/header.php';
?>
    <h1>Velkommen</h1>
    <form method="get" action="index.php">
        <select name="personer">
            <option selected="selected" disabled="disabled" value="">Velg kapasitet</option>
            <option value="2">2 personer</option>
            <option value="3">3 personer</option>
            <option value="4">4 personer</option>
        </select>
        /
        <select name="projektor">
            <option selected="selected" disabled="disabled" value="">Velg projektor</option>
            <option value="ja">Ja</option>
            <option value="nei">Nei</option>
        </select>
        /
        <input type="text" name="dato" id="dato" placeholder="Dato (dd.mm.åååå)" value="<?php echo dagensDato(); ?>">
        /
        <input type="submit" value="Finn rom">
    </form>
<?php
    $sql = $database->prepare("
        SELECT * FROM rom; 
    ");
    $sql->setFetchMode(PDO::FETCH_OBJ);
    $sql->execute();

    while ($element = $sql->fetch()) {
        echo '<div class="rom"><div class="romDetaljer">RomNr: ' . $element->romNr . ', Kapasitet: ' . $element->kapasitet . ', Projektor: ' . harProjektor($element->projektor) . '</div><div class="romTimer">';
        
        echo '<form action="bookRom.php" method="post">';
        
        $sql2 = $database->prepare("SELECT * FROM timer");
        $sql2->setFetchMode(PDO::FETCH_OBJ);
        $sql2->execute();
        
        $dato = "test";
        
        while ($tid = $sql2->fetch()) {
            echo '<input name="timer[]" value="' . $tid->timeID . '" type="checkbox">' . timesFormatering($tid->fraTid) . ' - ' . timesFormatering($tid->tilTid) . '<br />';
        }
        
        echo '<input type="hidden" name="romnr" value="' . $element->romNr . '">';
        echo '<input type="text" name="brukernavn" placeholder="Brukernavn" maxlength="10">';
        echo '<input type="text" name="password" placeholder="Passord">';
        echo '<br />';
        echo '<input type="submit" value="Hold av rom">';
        echo '</form>';
        
        echo '</div></div>';
    }
    require 'core/footer.php';
?>