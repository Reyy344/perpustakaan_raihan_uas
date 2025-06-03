<?php
session_start();
require_once '../config/db.php';

// Ubah status denda ke 'sudah dibayar'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ubah_denda_id'])) {
    $id_denda = (int) $_POST['ubah_denda_id'];
    $mysqli->query("UPDATE fines SET status = 'sudah dibayar' WHERE id = $id_denda");
    header("Location: denda.php");
    exit;
}

// Ambil semua denda
$denda = $mysqli->query("
    SELECT f.id AS fine_id, u.nama_lengkap, b.judul, f.jumlah_denda, f.status, f.tanggal_denda
    FROM fines f
    JOIN borrow_requests br ON f.borrow_request_id = br.id
    JOIN users u ON br.user_id = u.id
    JOIN books b ON br.book_id = b.id
    ORDER BY f.tanggal_denda DESC
");

// Hitung total denda belum dibayar
$total = $mysqli->query("SELECT SUM(jumlah_denda) AS total FROM fines WHERE status = 'belum dibayar'")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Denda</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
        button {
            padding: 5px 10px;
        }
        .status-belum { color: red; }
        .status-sudah { color: green; }
    </style>
</head>
<body>
    <header>
        <div class="logo">📚 UASPerpus</div>
        <div class="user">Halo, Admin</div>
    </header>

    <nav class="sidebar">
        <ul>
            <li><a href="../index.php">🏠 Dashboard</a></li>
            <li><a href="../users/list.php">👥 Manajemen Akun</a></li>
            <li><a href="../books/list.php">📖 Manajemen Buku</a></li>
            <li><a href="../loans/list.php">🔁 Manajemen Peminjaman</a></li>
            <li><a href="denda.php">💸 Manajemen Denda</a></li>
            <li><a href="../auth/logout.php" class="logout">🚪 Logout</a></li>
        </ul>
    </nav>

    <main class="content">
        <h1>💸 Manajemen Denda</h1>
        <p>Total Denda Belum Dibayar: <strong>Rp<?= number_format($total, 0, ',', '.') ?></strong></p>

        <table>
            <tr>
                <th>ID</th>
                <th>Nama User</th>
                <th>Judul Buku</th>
                <th>Jumlah Denda</th>
                <th>Status</th>
                <th>Tanggal Denda</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = $denda->fetch_assoc()): ?>
            <tr>
                <td><?= $row['fine_id'] ?></td>
                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                <td><?= htmlspecialchars($row['judul']) ?></td>
                <td>Rp<?= number_format($row['jumlah_denda'], 0, ',', '.') ?></td>
                <td class="<?= $row['status'] === 'belum dibayar' ? 'status-belum' : 'status-sudah' ?>">
                    <?= $row['status'] === 'belum dibayar' ? '❌ Belum Dibayar' : '✅ Sudah Dibayar' ?>
                </td>
                <td><?= $row['tanggal_denda'] ?></td>
                <td>
                    <?php if ($row['status'] === 'belum dibayar'): ?>
                        <form method="post" action="">
                            <input type="hidden" name="ubah_denda_id" value="<?= $row['fine_id'] ?>">
                            <button type="submit">Tandai Lunas</button>
                        </form>
                    <?php else: ?>
                        ✔
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </main>

    <footer>
        &copy; <?= date("Y") ?> UAS Perpus - Sistem Informasi Perpustakaan Sekolah
    </footer>
</body>
</html>