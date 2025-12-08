<?php require "../config/database.php"; session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Mahasiswa</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="sistem-a">

<div class="login-container">
    <div class="login-header">
        <h1>📚 Sistem Perpustakaan</h1>
        <p>Silakan login dengan akun mahasiswa Anda</p>
    </div>

    <form action="" method="POST">
        <div class="form-group">
            <label>NIM</label>
            <input type="text" name="nim" placeholder="Masukkan NIM Anda" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password Anda" required>
        </div>

        <button type="submit" class="btn-primary">Login</button>
    </form>

    <div class="divider">atau</div>

    <a href="./register.php" class="btn-secondary">Daftar Akun Baru</a>

    <?php
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $nim = $_POST['nim'];
        $pass = $_POST['password'];

        $sql = $conn->query("SELECT * FROM mahasiswa WHERE nim='$nim'");
        $mhs = $sql->fetch_assoc();

        if($mhs && password_verify($pass, $mhs['password'])){
            $_SESSION['mhs_id'] = $mhs['id'];
            header("Location: ./dashboard.php");
        } else {
            echo "<div class='alert alert-error'>❌ NIM atau password salah!</div>";
        }
    }
    ?>
</div>

</body>
</html>