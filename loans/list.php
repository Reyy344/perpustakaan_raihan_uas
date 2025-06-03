<?php
session_start();
require '../config/db.php';
$res = $mysqli->query(
    "select br.id,u.username,b.judul,br.request_at, br.picked_up, br.returned
   from borrow_requests br
   join users u on br.user_id=u.id
   join books b on br.book_id=b.id"
);
?>
<link rel="stylesheet" href="style.css">
<a class="backButton" href="../index.php">Kembali</a>
<table border=1>
    <h1>List Peminjaman Buku</h1>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Buku</th>
        <th>Tgl Request</th>
        <th>Pickup</th>
        <th>Return</th>
        <th>Aksi</th>
    </tr>
    <?php while ($r = $res->fetch_assoc()): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['username'] ?></td>
            <td><?= $r['judul'] ?></td>
            <td><?= $r['request_at'] ?></td>
            <td><?= $r['picked_up'] ? 'Yes' : 'No' ?></td>
            <td><?= $r['returned'] ? 'Yes' : 'No' ?></td>
            <td><a href="update.php?id=<?= $r['id'] ?>">Ubah Status</a></td>
        </tr>
    <?php endwhile; ?>
</table>