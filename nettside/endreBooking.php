<?php
    $tittel = 'Endre booking';
    require 'core/init.php';
    
    // Start på tabell-script
    if (isset($_POST)) {
        if ($_POST['submit'] == "Hent mine bookinger") {
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
            
            if (!isset($error)) {
                if (brukerFinnes($brukernavn, $passord)) {
                    echo 'Suksess';
                } else {
                    $error[] = "Feil brukernavn eller passord.";   
                }
            }
        } else if ($_POST['submit'] == "Fjern booking") {
            echo 'Funksjon utilgjengelig';
        }
    }
    // Slutt på tabell-script

    require 'core/header.php';
    if (isset($_GET['suksess']) && empty($_GET['suksess'])) {
        echo '<script type="text/javascript">alert("Suksess, du har booket et rom.");</script>';
    } else if (isset($_GET['error']) && !empty($_GET['error'])) {
        echo '<script type="text/javascript">alert("' . $_GET['error'] . '");</script>';
    }
?>
    <h1>Endre din booking</h1>
    <p>Skriv inn ditt brukernavn og passord for å få en oversikt over hvilke rom du har booket.</p>
    <?php if (isset($error)) print_r($error); ?>
    <form method="post" action="endreBooking.php" id="hentDinTabell">
        <input type="text" name="brukernavn" placeholder="Brukernavn" maxlength="10" <?php if (isset($brukernavn) && !empty($brukernavn)) echo 'value="' . $brukernavn . '"'; ?> />
        <input type="password" name="passord" placeholder="Passord" maxlength="30" />
        <input type="submit" name="submit" value="Hent mine bookinger" />
    </form>
<?php
    require 'core/footer.php';
?>