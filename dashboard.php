<?php
session_start();
include "koneksi.php";
 
// halaman ini hanya untuk yang sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php?error=" . urlencode("Silakan login dulu."));
    exit;
}
 
$pesan_info = '';
 
// tambah tamu
if (isset($_POST['simpan'])) {
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');
 
    if ($nama === '' || $email === '' || $pesan === '') {
        $pesan_info = "Semua kolom wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pesan_info = "Format email tidak valid.";
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO tamu (nama, email, pesan) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $nama, $email, $pesan);
        mysqli_stmt_execute($stmt);
        header("Location: dashboard.php?status=tersimpan");
        exit;
    }
}
 
// hapus tamu
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $stmt = mysqli_prepare($koneksi, "DELETE FROM tamu WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: dashboard.php?status=terhapus");
    exit;
}
 
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'tersimpan') $pesan_info = "Pesan tamu berhasil disimpan.";
    if ($_GET['status'] === 'terhapus')  $pesan_info = "Pesan tamu berhasil dihapus.";
}
 
$query = mysqli_query($koneksi, "SELECT * FROM tamu ORDER BY created_at DESC");
$total = mysqli_num_rows($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Buku Tamu</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="topbar">
        <h1>Buku Tamu</h1>
        <div>
            <span>Halo, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php" class="tombol-keluar">Keluar</a>
        </div>
    </header>
 
    <main class="isi">
        <?php if ($pesan_info): ?>
            <div class="alert"><?= htmlspecialchars($pesan_info) ?></div>
        <?php endif; ?>
 
        <section class="panel">
            <h2>Tambah pesan tamu</h2>
            <form method="post">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" required>
 
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
 
                <label for="pesan">Pesan</label>
                <textarea id="pesan" name="pesan" rows="4" required></textarea>
 
                <button type="submit" name="simpan">Simpan pesan</button>
            </form>
        </section>
 
        <section class="panel">
            <h2>Daftar tamu (<?= $total ?>)</h2>
            <?php if ($total === 0): ?>
                <p class="kosong">Belum ada pesan. Isi form di atas untuk menambahkan tamu pertama.</p>
            <?php else: ?>
                <div class="tabel-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Pesan</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['pesan'])) ?></td>
                                <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <a class="hapus" href="dashboard.php?hapus=<?= $row['id'] ?>"
                                       onclick="return confirm('Hapus pesan dari <?= htmlspecialchars($row['nama'], ENT_QUOTES) ?>?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>