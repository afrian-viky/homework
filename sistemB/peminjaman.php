<?php
$url = "http://localhost/perpus/sistemA/api/peminjaman.php";
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peminjaman</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="sistem-b">

<header>🏛️ Aplikasi Petugas Perpustakaan</header>

<div class="nav">
    <a href="./mahasiswa.php">👥 Data Mahasiswa</a>
    <a href="./peminjaman.php">📚 Data Peminjaman</a>
</div>

<div class="container">
    <h2 class="page-title">📚 Data Peminjaman Buku</h2>

    <table>
        <tr>
            <th>Mahasiswa</th>
            <th>NIM</th>
            <th>Buku</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php 
        if($data && count($data) > 0){
            foreach($data as $p): 
        ?>
        <tr>
            <td><?= $p["mahasiswa"] ?></td>
            <td><?= $p["nim"] ?></td>
            <td><?= $p["buku"] ?></td>
            <td><span class="status-<?= strtolower($p['status']) ?>"><?= $p["status"] ?></span></td>
            <td>
                <div class="action-buttons">
                    <a href="./approve.php?id=<?= $p['id'] ?>" class="btn-approve">✅ Approve</a>
                    <a href="./reject.php?id=<?= $p['id'] ?>" class="btn-reject">❌ Reject</a>
                </div>
            </td>
        </tr>
        <?php 
            endforeach;
        } else {
            echo "<tr><td colspan='5' style='text-align:center; padding:30px; color:#9ca3af;'>Tidak ada data peminjaman</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>