<?php
session_start();
require_once 'config/db.php';
if (!isset($_SESSION['role']) && $_SESSION['role'] !== 'anggota') {
  header("Location: ../auth/login.php?error=Akses ditolak");
      exit();
  }

$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';
if ($search) {
    $result = $mysqli->query("SELECT * FROM books WHERE judul LIKE '%$search%' ORDER BY created_at DESC");
} else {
    $result = $mysqli->query("SELECT * FROM books ORDER BY created_at DESC");
} 
if (!$result) {
    die("Error: " . mysqli_error($conn));
}  
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Anggota</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .book-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .book-card img {
            width: 100%;
            height: 230px;
            object-fit: cover;
            background-color: #f0f0f0;
        }
        .book-card .info {
            padding: 10px;
            text-align: center;
        }
        .book-card .info h3 {
            font-size: 16px;
            margin: 10px 0 5px;
        }
        .book-card .info p {
            font-size: 14px;
            margin: 4px 0;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .btn-pinjam {
            background-color: #007bff;
            color: white;
        }
        .btn-habis {
            background-color: #ccc;
            color: #444;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
  <header>
    <div class="logo">📚 UASPerpus</div>
    <div class="user">Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?> 👋</div>
  </header>

  <nav class="sidebar">
    <ul>
      <li><a href="#">📖 Buku</a></li>
      <li><a href="historyloans/history.php">🔁 Riwayat Peminjaman</a></li>
      <li><a href="auth/logout.php" class="logout">🚪 Logout</a></li>
    </ul>
  </nav>

  <main class="content">
    <h1>Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?> 👋</h1>
    <p>Selamat Datang di aplikasi UASPerpus, Tempat untuk membaca dan meminjam buku!</p>

    <div class="cards">
      <div class="card blue">
        <h2>📖 Buku</h2>
        <p>Koleksi Buku!</p>
      </div>
          <div class="card green">
            <h2>🔁 Riwayat Peminjaman</h2>
            <p>History Peminjaman Anda!</p>
          </div>
    </div>

    <h1 style="margin-top: 30px; text-align: left;">📚 Daftar Buku Tersedia</h1>

    <form method="GET" style="margin-top: 20px;">
    <input type="text" name="search" placeholder="Cari buku berdasarkan judul..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" style="padding: 8px; width: 300px; border-radius: 5px; border: 1px solid #ccc;">
    <button type="submit" style="padding: 8px 12px; border: none; border-radius: 5px; background-color: #007bff; color: white;">🔍 Cari</button>
</form>
    <div class="book-grid">
            <?php while ($book = $result->fetch_assoc()): ?>
                <div class="book-card">
                    <img src="<?= $book['cover'] ? ($book['cover']) : '../assets/img/default.jpg' ?>" alt="<?= htmlspecialchars($book['judul']) ?>">
                    <div class="info">
                        <h3><?= htmlspecialchars($book['judul']) ?></h3>
                        <p><strong>Penulis:</strong> <?= htmlspecialchars($book['penulis']) ?></p>
                        <p><strong>Stok:</strong> <?= $book['stok'] ?></p>
                        <?php if ($book['stok'] > 0): ?>
                            <a href="loans/pinjam.php?book_id=<?= $book['id'] ?>" class="btn btn-pinjam">📘 Pinjam Buku</a>
                        <?php else: ?>
                            <span class="btn btn-habis">❌ Stok Habis</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
  </main>

  <footer>
    &copy; <?= date("Y") ?> UAS Perpus - Sistem Informasi Perpustakaan Sekolah
  </footer>
</body>
</html>