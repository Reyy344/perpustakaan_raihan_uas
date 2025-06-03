<?php
$host = 'localhost';
$db = 'perpustakaan_db';
$user = 'root';
$pass = '';
$port = 3306;
$mysqli = new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_error) {
    die('Koneksi gagal: ' . $mysqli->connect_error);
}
?>