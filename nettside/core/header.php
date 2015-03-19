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
        
        <!-- Denne css-filen er hentet fra en annen side, det er et tema for jQuery kalenderen som vi bruker -->
        <link rel="stylesheet" href="https://raw.githubusercontent.com/rtsinani/jquery-datepicker-skins/master/css/melon.datepicker.css" />
        <script type="text/javascript">
            // Dette scriptet legger til en kalender på dato-input med jQuery.
            $(function() {
                $('#dato').datepicker({
                    dateFormat: 'dd.mm.yy',
                    autoSize: true,
                    minDate: 0,
                    maxDate: 30,
                    onSelect: function (dateText, inst) {
                        $(this).parent('form').submit();
                    },
                    showOtherMonths: true,  
                    dayNamesMin: ['Søn', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'Lør'],  
                    monthNames: ['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember'],  
                });
            });
        </script>
        <!-- Dette scriptet åpner rom boks ved bruk av onclick -->
        <script type="text/javascript">
            function aapne($id) {
                if ($($id).hasClass('aktiv')) {
                    $($id).removeClass('aktiv');
                    $($id).addClass('inaktiv');
                } else{
                    $('.rom').removeClass('aktiv');
                    $('.rom').addClass('inaktiv');
                    $($id).removeClass('inaktiv');
                    $($id).addClass('aktiv');
                }
            }
        </script>
        
        <!-- Dette skriptet markerer alle timer, ved bruk av onclick-->
        <script type="text/javascript">
            function velgAlle($id) {
                if ($($id + ' .romTimer form input:checkbox').prop('checked')) {
                    $($id + ' .romTimer form input:checkbox').prop('checked', false);
                } else {
                    $($id + ' .romTimer form input:checkbox').prop('checked', true);
                }
            }
        </script>
        
        <!-- Dette skriptet ser etter anker i URL, og bruker denne for å åpne rom boks -->
        <script type="text/javascript">
            window.onload = function() {
                if (window.location.hash) {
                    $id = window.location.hash;
                    aapne($id);
                }
            };
        </script>
        
        <!-- Skript for popup advarsel om du er sikker på fjerning av reservasjon -->
        <script type="text/javascript">
            function avbestilling() { 
                return confirm("Er du sikker på at du vil fjerne denne reservasjonen?"); 
            }
        </script>
        <!-- JS slutt -->
    </head>
    <body>
        <div id="container">
            <div id="header">
                <a href="hjem"><img src="img/westerdalsLogoSidestilt.png" id="logo" alt="Westerdals Oslo ACT" /></a>
                <div id="menu">
                    <ul>
                        <!-- Bruker php for å sjekke om siden er "aktiv", altså om det er den man er på -->
                        <li <?php if ($tittel == "Book rom") echo 'class="aktiv"'; ?>></l><a href="hjem">Book rom</a></li>
                        <li <?php if ($tittel == "Endre booking") echo 'class="aktiv"'; ?>><a href="endreBooking">Redigere booking</a></li>
                    </ul>
                </div>
            </div>