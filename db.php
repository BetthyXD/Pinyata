<?php
function db_connect(){
$server = "localhost";
$uzivatel = "betthy";
$heslo = "40nigaguuC8ItS";
$databaze = "reminder";

    $con = mysqli_connect($server, $uzivatel, $heslo, $databaze);
    mysqli_set_charset($con, "utf8");
    return $con;
}
?>