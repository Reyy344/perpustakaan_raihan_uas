<?php
session_start();
require '../config/db.php';
$id = (int) $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pu = isset($_POST['picked_up']) ? 1 : 0;
    $ret = isset($_POST['returned']) ? 1 : 0;
    $ret_at = $ret ? ", return_at=now()" : "";
    $mysqli->query("update borrow_requests set picked_up={$pu}, returned={$ret} {$ret_at} where id={$id}");
    header('Location: list.php');
    exit;
}
$r = $mysqli->query("select * from borrow_requests where id={$id}")->fetch_assoc();
?>
<link rel="stylesheet" href="styleupdate.css">
<form method="post">
    <label><input type="checkbox" name="picked_up" <?= $r['picked_up'] ? 'checked' : '' ?>> Picked Up</label><br>
    <label><input type="checkbox" name="returned" <?= $r['returned'] ? 'checked' : '' ?>> Returned</label><br>
    <button>Save</button>
</form>