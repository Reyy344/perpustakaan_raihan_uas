<?php
session_start();
require '../config/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $stmt = $mysqli->prepare("insert into books(judul,penulis,penerbit,tahun_terbit,stok,cover) values(?,?,?,?,?,?)");
    $stmt->bind_param('sssiss', $judul, $penulis, $penerbit, $tahun, $stok, $cover);
    $stmt->execute();
    header('Location: list.php');
    exit;
}
?>
<form method="post">
    <link rel="stylesheet" href="style.css">
    Judul: <input name="judul"><br>
    Penulis: <input name="penulis"><br>
    Penerbit: <input name="penerbit"><br>
    Tahun: <input name="tahun" type="number"><br>
    Stok: <input name="stok" type="number"><br>
    Cover URL: <input name="cover"><br>
    <button>Save</button>
</form>