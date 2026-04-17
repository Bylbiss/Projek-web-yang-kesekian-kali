<?php
$host = "localhost";
$user = "root";
$pass = "";
$database = "otw_dokter";

$koneksi = mysqli_connect($host, $user, $pass, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

?>