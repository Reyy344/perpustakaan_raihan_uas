<?php
session_start();
require_once '../config/db.php';

if (!isset($_GET['book_id'])) {
    die("ID buku tidak ditemukan.");
}
$book_id = (int) $_GET['book_id'];
$buku = $mysqli->query("SELECT * FROM books WHERE id = $book_id");

if ($buku->num_rows !== 1) {
    die("Buku tidak ditemukan.");
}

$book = $buku->fetch_assoc();
$user_id = $_SESSION['user_id']; // user default

// Set default tanggal pinjam dan kembali
$today = date('Y-m-d');
$default_kembali = date('Y-m-d', strtotime('+7 days'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($book['stok'] < 1) {
        die("Stok buku habis.");
    }

    // Ambil tanggal dari form, validasi sederhana
    $tanggal_pinjam = $_POST['tanggal_pinjam'] ?? $today;
    $tanggal_kembali = $_POST['tanggal_kembali'] ?? $default_kembali;

    // Simpan ke borrow_requests
    $stmt = $mysqli->prepare("INSERT INTO borrow_requests (user_id, book_id, request_at, return_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $book_id, $tanggal_pinjam, $tanggal_kembali);
    $insert = $stmt->execute();

    if ($insert) {
        $mysqli->query("UPDATE books SET stok = stok - 1 WHERE id = $book_id");
        header("Location: ../index2.php?success=Peminjaman berhasil");
    } else {
        header("Location: ../index2.php?error=Gagal meminjam buku");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Konfirmasi Peminjaman</title>
    <style>
        body { font-family: sans-serif; padding: 40px; background-color: #f9f9f9; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,.1); }
        h2 { margin-top: 0; }
        .btn {
            padding: 10px 20px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-kembali { background-color: #ccc; color: black; text-decoration: none; }
        .btn-pinjam { background-color: #007bff; color: white; }
        label { display: block; margin-top: 15px; }
        input[type=date] { padding: 8px; width: 200px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Konfirmasi Peminjaman Buku</h2>
        <p><strong>Judul Buku:</strong> <?= htmlspecialchars($book['judul']) ?></p>
        <p><strong>Penulis:</strong> <?= htmlspecialchars($book['penulis']) ?></p>
        <p><strong>Stok:</strong> <?= $book['stok'] ?></p>

        <?php if ($book['stok'] < 1): ?>
            <p style="color: red;">❌ Maaf, stok buku ini habis.</p>
            <a href="../index2.php" class="btn btn-kembali">⬅ Kembali</a>
        <?php else: ?>
            <form method="post">
                <label for="tanggal_pinjam">Tanggal Peminjaman:</label>
                <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" value="<?= $today ?>" required />

                <label for="tanggal_kembali">Tanggal Pengembalian:</label>
                <input type="date" id="tanggal_kembali" name="tanggal_kembali" value="<?= $default_kembali ?>" required />

                <button type="submit" class="btn btn-pinjam">📘 Konfirmasi Peminjaman</button>
                <a href="../index2.php" class="btn btn-kembali">⬅ Batal</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>