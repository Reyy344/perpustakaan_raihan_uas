<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u = $mysqli->real_escape_string($_POST['username']);
    $p = md5($_POST['password']);
    
    $res = $mysqli->query("SELECT * FROM users WHERE username='{$u}' AND password = '$p'");

    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc(); {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];


            session_commit();

            // Redirect berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: ../index.php");
            }
            elseif ($user['role'] == 'staff') {
            header("Location: ../index1.php");
        } elseif ($user['role'] == 'anggota') {
            header("Location: ../index2.php");
        } else {
            header("Location: ../index.php");
        }
        exit();
        }
    }

    $error = 'Username/Password salah atau akun belum aktif.';
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>

            
        </form>
    </div>
</body>
</html>
