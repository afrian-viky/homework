<?php 
session_start(); 
if(!isset($_SESSION['mhs_id'])){ header("Location: login.php"); exit; }

require "../config/database.php";
$id = $_SESSION['mhs_id'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman</title>
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
    <h2 class="page-title">📋 Riwayat Peminjaman Saya</h2>

    <table>
        <tr>
            <th>Judul Buku</th>
            <th>Status</th>
        </tr>

        <?php
        $sql = $conn->query("SELECT b.judul, p.status 
                             FROM peminjaman p
                             JOIN buku b ON p.buku_id=b.id
                             WHERE p.mahasiswa_id=$id
                             ORDER BY p.id DESC");

        if($sql->num_rows > 0){
            while($p = $sql->fetch_assoc()){
                $statusClass = "status-" . strtolower($p['status']);
                echo "
                <tr>
                    <td>{$p['judul']}</td>
                    <td><span class='$statusClass'>{$p['status']}</span></td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='2' style='text-align:center; padding:30px; color:#9ca3af;'>Belum ada riwayat peminjaman</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>