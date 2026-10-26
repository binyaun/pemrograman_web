<?php

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbs3alatih";

    $connect = new mysqli($servername, $username, $password, $dbname);
    $connect->set_charset('utf8mb4');
?>