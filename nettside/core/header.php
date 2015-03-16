<!DOCTYPE html>
<html lang="no">
    <head>
        <meta charset="utf-8" />
        <!-- Bruker PHP for å hente tittelen på gjeldende side -->
        <title><?php echo $tittel; ?> - Westerdals Oslo ACT</title>
        <meta name="author" content="Gruppe 36 - PJ2100">
		<meta http-equiv="X-UA-Compatible" content="IE=9" />
		<link rel="shortcut icon" href="img/icon.png" type="image/x-icon" />
        <link rel="stylesheet" href="css/style.css" type="text/css" />
        
        <!-- Font Awesome ikoner -->
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        
        <!-- JS start -->
        <!-- Henter inn et jQuery rammeverk, selve jQuery, og ekstra UI som trengs for .datepicker funksjonen til jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
        
        <!-- Dette er et script som skal gjøre HTML5 fungerende i eldre IE -->
        <!--[if lt IE 9]>
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script>
            // Dette scriptet legger til en kalender på dato-input med jQuery.
            $(function() {
                $('#dato').datepicker({
                    dateFormat: 'dd.mm.yy'
                });
            });
        </script>
        <script>
            // Dette scriptet utfører drop-down på rommene (fjerner/legger til en klasse) vie jQuery.
            $(document).ready(function() {
                $('.rom.inaktiv').on('click', aktivtRom);
            });

            function aktivtRom() {
                $('.rom').removeClass('aktiv');
                $('.rom').addClass('inaktiv');
                $(this).removeClass('inaktiv');
                $(this).addClass('aktiv');
            }
        </script>
        <!-- JS slutt -->
    </head>
    <body>
        <div id="container">
            <div id="header">
                <a href="index.php"><img src="img/westerdalsLogoSidestilt.png" id="logo" alt="Westerdals Oslo ACT" /></a>
                <div id="menu">
                    <ul>
                        <!-- Bruker php for å sjekke om siden er "aktiv", altså om det er den man er på -->
                        <li <?php if ($tittel == "Book rom") echo 'class="aktiv"'; ?>></l><a href="index.php">Book rom</a></li>
                        <li <?php if ($tittel == "Endre booking") echo 'class="aktiv"'; ?>><a href="endreBooking.php">Redigere booking</a></li>
                    </ul>
                </div>
            </div>