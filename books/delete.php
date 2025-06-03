<?php
require '../config/db.php';
$mysqli->query("delete from books where id=" . (int) $_GET['id']);
header('Location: list.php');
exit;
?>