<?php
session_start();
require '../config/db.php';
$id = (int) $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahun = (int) $_POST['tahun'];
    $stok = (int) $_POST['stok'];
    $cover = $_POST['cover'];

    // Update pakai prepared statement
    $stmt = $mysqli->prepare("UPDATE books SET judul = ?, penulis = ?, penerbit = ?, tahun_terbit = ?, stok = ?, cover = ? WHERE id = ?");
    $stmt->bind_param("sssissi", $judul, $penulis, $penerbit, $tahun, $stok, $cover, $id);

    if ($stmt->execute()) {
        header("Location: list.php");
        exit;
    } else {
        echo "Gagal update: " . $stmt->error;
    }
}

// Ambil data buku
$b = $mysqli->query("SELECT * FROM books WHERE id = $id")->fetch_assoc();
?>
<form method="post">
    <link rel="stylesheet" href="style.css">
    Judul: <input name="judul" value="<?= $b['judul'] ?>"><br>
    Penulis: <input name="penulis" value="<?= $b['penulis'] ?>"><br>
    Penerbit: <input name="penerbit" value="<?= $b['penerbit'] ?>"><br>
    Tahun: <input name="tahun" type="number" value="<?= $b['tahun_terbit'] ?>"><br>
    Stok: <input name="stok" type="number" value="<?= $b['stok'] ?>"><br>
    Cover URL: <input name="cover" value="<?= $b['cover'] ?>"><br>
    <!-- lainnya serupa -->
    <button>Update</button>
</form>