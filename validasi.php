<?php
session_start();
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header("Location: index.php?error=" . urlencode("Username dan password wajib diisi."));
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT id_user, username, password FROM `user` WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$data  = mysqli_fetch_assoc($hasil);

if ($data && $password === $data['password']) {
    session_regenerate_id(true);
    $_SESSION['user_id']  = $data['id_user'];
    $_SESSION['username'] = $data['username'];
    header("Location: dashboard.php");
    exit;
}

header("Location: index.php?error=" . urlencode("Username atau password salah."));
exit;