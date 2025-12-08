<?php require "../config/database.php"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Mahasiswa</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="sistem-a">

<div class="login-container">
    <div class="login-header">
        <h1>📝 Daftar Akun Baru</h1>
        <p>Lengkapi form di bawah untuk membuat akun</p>
    </div>

    <form action="" method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" placeholder="Masukkan nama lengkap" required>
        </div>

        <div class="form-group">
            <label>NIM</label>
            <input type="text" name="nim" placeholder="Masukkan NIM" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Buat password" required>
        </div>

        <button type="submit" class="btn-primary">Daftar Sekarang</button>
    </form>

    <div class="divider">atau</div>

    <a href="./login.php" class="btn-secondary">Sudah Punya Akun? Login</a>

    <?php
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $nama = $_POST['nama'];
        $nim = $_POST['nim'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $conn->query("INSERT INTO mahasiswa(nama, nim, password)
                      VALUES('$nama', '$nim', '$pass')");

        echo "<div class='alert alert-success'>✅ Registrasi berhasil! Silakan login.</div>";
    }
    ?>
</div>

</body>
</html>