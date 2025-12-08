<?php
$url = "http://localhost/perpus/sistemA/api/mahasiswa.php";

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
    <title>Data Mahasiswa</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="sistem-b">

<header>🏛️ Aplikasi Petugas Perpustakaan</header>

<div class="nav">
    <a href="./mahasiswa.php">👥 Data Mahasiswa</a>
    <a href="./peminjaman.php">📚 Data Peminjaman</a>
</div>

<div class="container">
    <h2 class="page-title">👥 Data Mahasiswa Terdaftar</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>NIM</th>
        </tr>

        <?php 
        if($data && count($data) > 0){
            foreach($data as $m): 
        ?>
        <tr>
            <td><?= $m["id"] ?></td>
            <td><?= $m["nama"] ?></td>
            <td><?= $m["nim"] ?></td>
        </tr>
        <?php 
            endforeach;
        } else {
            echo "<tr><td colspan='3' style='text-align:center; padding:30px; color:#9ca3af;'>Tidak ada data mahasiswa</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>