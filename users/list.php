<?php
session_start();
require_once '../config/db.php';
$users = $mysqli->query("select * from users where role='anggota'");
?>
<link rel="stylesheet" href="style.css">
<a class="backButton" href="../index.php">Kembali</a>
<h2>List Akun Anggota</h2>
<a href="../auth/logout.php">Logout</a>
<table border=1>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Nama</th>
        <th>Aktif?</th>
        <th>Aksi</th>
    </tr>
    <?php while ($u = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= $u['username'] ?></td>
            <td><?= $u['nama_lengkap'] ?></td>
            <td><?= $u['is_active'] ? 'Ya' : 'Tidak' ?></td>
            <td>
                <a href="activate.php?id=<?= $u['id'] ?>&set=<?= $u['is_active'] ? 0 : 1 ?>">
                    <?= $u['is_active'] ? 'Deaktivasi' : 'Aktivasi' ?>
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>