<?php
session_start();
 
// kalau sudah login, langsung ke dashboard
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}
 
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Buku Tamu</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="halaman-login">
    <main class="kotak-login">
        <h1>Buku Tamu</h1>
        <p class="sub">Masuk untuk melihat dan mengelola pesan tamu.</p>
 
        <?php if ($error): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
 
        <form action="validasi.php" method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus>
 
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
 
            <button type="submit">Masuk</button>
        </form>
    </main>
</body>
</html>