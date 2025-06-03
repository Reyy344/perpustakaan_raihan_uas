<?php
$host = 'bvqlqneqaisdv0ra6bww-mysql.services.clever-cloud.com';
$db = 'bvqlqneqaisdv0ra6bww';
$user = 'uduak0nhvnjxwhoc';
$pass = 'HAWZQRVGGDOrgsDCNdnf';
$port = 3306;
$mysqli = new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_error) {
    die('Koneksi gagal: ' . $mysqli->connect_error);
}
?>