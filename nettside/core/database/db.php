<?php
    $host = "localhost";
    $db = "PJ2100";
    $bruker = "root";
    $pass = "root";

    $database = new PDO("mysql:host=$host;dbname=$db", "$bruker", "$pass");
?>