<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['admin_id']))
    header('Location: ../auth/login.php');
$id = (int) $_GET['id'];
$set = (int) $_GET['set'];
$mysqli->query("update users set is_active={$set} where id={$id}");
header('Location: list.php');
exit;