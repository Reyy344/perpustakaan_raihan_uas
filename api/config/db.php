<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'perpustakaan_db';
$port = 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}
?>