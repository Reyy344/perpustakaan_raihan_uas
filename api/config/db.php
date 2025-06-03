<?php
$host = 'bvqlqneqaisdv0ra6bww-mysql.services.clever-cloud.com';
$dbname = 'bvqlqneqaisdv0ra6bww';
$user = 'uduak0nhvnjxwhoc';
$pass = 'HAWZQRVGGDOrgsDCNdnf';
$port = 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}
?>