<?php 
session_start(); 
if(!isset($_SESSION['mhs_id'])){ header("Location: ./login.php"); exit; }

require "../config/database.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="sistem-a">

<header>📚 Aplikasi Peminjaman Buku - Mahasiswa</header>

<div class="nav">
    <a href="./dashboard.php">📖 Daftar Buku</a>
    <a href="./riwayat.php">📋 Riwayat Peminjaman</a>
    <a href="./logout.php">🚪 Logout</a>
</div>

<div class="container">
    <h2 class="page-title">Daftar Buku Tersedia</h2>

    <div class="card">
        <h3>Pilih Buku untuk Dipinjam</h3>
        <form action="" method="POST">
            <select name="buku_id">
                <?php
                // Tampilkan buku dengan stok > 0
                $buku = $conn->query("SELECT * FROM buku WHERE stok > 0");
                while($b = $buku->fetch_assoc()){
                    echo "<option value='{$b['id']}'>{$b['judul']} (Stok: {$b['stok']})</option>";
                }
                ?>
            </select>

            <button type="submit">📚 Pinjam Buku Ini</button>
        </form>
    </div>

    <?php
    if($_SERVER['REQUEST_METHOD'] === "POST"){
        $buku = $_POST['buku_id'];
        $mhs = $_SESSION['mhs_id'];

        $conn->query("INSERT INTO peminjaman(mahasiswa_id, buku_id)
                      VALUES($mhs, $buku)");

        echo "<div class='alert alert-success'>✅ Berhasil mengajukan peminjaman! Silakan tunggu persetujuan petugas.</div>";
    }
    ?>
</div>

</body>
</html>