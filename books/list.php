<?php
session_start();
require '../config/db.php';
$res = $mysqli->query("select * from books");
?>
<link rel="stylesheet" href="style.css">
<a class="addButton" href="add.php">Tambah Buku</a>
<a class="backButton" href="../index.php">Kembali</a>
<h2>List Buku</h2>
<table border=1>
    <tr>
        <th>ID</th>
        <th>Judul</th>
        <th>Penulis</th>
        <th>Stok</th>
        <th>Aksi</th>
    </tr>
    <?php while ($b = $res->fetch_assoc()): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= $b['judul'] ?></td>
            <td><?= $b['penulis'] ?></td>
            <td><?= $b['stok'] ?></td>
            <td>
                <a href="edit.php?id=<?= $b['id'] ?>">Edit</a> |
                <a href="delete.php?id=<?= $b['id'] ?>" onclick="return confirm('Hapus?')">Hapus</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>