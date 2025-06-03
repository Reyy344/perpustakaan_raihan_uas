<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'anggota') {
    header("Location: auth/login.php?error=Akses ditolak");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "
    SELECT br.*, b.judul, b.penulis 
    FROM borrow_requests br
    JOIN books b ON br.book_id = b.id
    WHERE br.user_id = ?
    ORDER BY br.request_at DESC
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Peminjaman</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 40px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px auto;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        td {
            color: #333;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .status {
            font-weight: bold;
        }
        .status.returned {
            color: green;
        }
        .status.pending {
            color: orange;
        }
        a.back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            background-color: #007bff;
            padding: 15px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
        }
        a.back-link:hover {
            opacity: 80%;
        }
        @media (max-width: 600px) {
            table, th, td {
                font-size: 14px;
            }
            body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <h2>🔁 Riwayat Peminjaman</h2>
    <table>
        <thead>
            <tr>
                <th>Judul Buku</th>
                <th>Penulis</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['judul']) ?></td>
                    <td><?= htmlspecialchars($row['penulis']) ?></td>
                    <td><?= date('d-m-Y', strtotime($row['request_at'])) ?></td>
                    <td><?= $row['return_at'] ? date('d-m-Y', strtotime($row['return_at'])) : '-' ?></td>
                    <td>
                        <?= $row['returned'] ? '✅ Dikembalikan' : '⏳ Belum kembali' ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="../index2.php" class="back-link">⬅ Kembali ke Dashboard</a>
</body>
</html>