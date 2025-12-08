<?php
// Konfigurasi Database
$host = 'localhost';
$dbname = 'sql_injection_demo';
$username = 'root';
$password = '';

// Koneksi Database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Fungsi untuk eksekusi multiple queries (VULNERABLE - untuk demo saja!)
function executeMultiQuery($pdo, $query) {
    $mysqli = new mysqli('localhost', 'root', '', 'sql_injection_demo');
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    $result = $mysqli->multi_query($query);
    
    // Clear all result sets
    do {
        if ($res = $mysqli->store_result()) {
            $res->free();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());
    
    $mysqli->close();
    
    return $result;
}

// Inisialisasi Pesan
$message1 = $message2 = $message3 = '';
$queryExecuted1 = '';

// FORM 1: Login VULNERABLE (Tanpa Prepared Statement & Filter)
if(isset($_POST['login1'])) {
    $user = $_POST['username1'];
    $pass = $_POST['password1'];
    
    // VULNERABLE: Query langsung tanpa proteksi
    $query = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    $queryExecuted1 = $query;
    
    try {
        // Gunakan mysqli untuk multiple queries
        $result = executeMultiQuery($pdo, $query);
        
        if($result) {
            // Cek apakah ada injection (cek apakah ada ; dalam query)
            if(strpos($query, ';') !== false) {
                $message1 = "✅ Query BERHASIL dieksekusi! Cek tabel di bawah untuk melihat perubahan.";
            } else {
                // Query biasa (SELECT untuk login)
                $checkLogin = $pdo->query($query);
                if($checkLogin && $checkLogin->rowCount() > 0) {
                    $message1 = "✅ Login BERHASIL! (VULNERABLE)";
                } else {
                    $message1 = "❌ Login GAGAL! (VULNERABLE)";
                }
            }
        } else {
            $message1 = "❌ Query GAGAL dieksekusi!";
        }
    } catch(Exception $e) {
        $message1 = "⚠️ Query Error: " . $e->getMessage();
    }
    
    $message1 .= "<br><small><strong>Query dieksekusi:</strong><br>" . htmlspecialchars($query) . "</small>";
}

// FORM 2: Login dengan Prepared Statement
if(isset($_POST['login2'])) {
    $user = $_POST['username2'];
    $pass = $_POST['password2'];
    
    // AMAN: Menggunakan Prepared Statement
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$user, $pass]);
    
    if($stmt->rowCount() > 0) {
        $message2 = "✅ Login BERHASIL! (Prepared Statement)";
    } else {
        $message2 = "❌ Login GAGAL! (Prepared Statement)";
    }
}

// FORM 3: Login dengan Prepared Statement + Input Filter
if(isset($_POST['login3'])) {
    // Filter Input
    $user = htmlspecialchars(trim($_POST['username3']), ENT_QUOTES, 'UTF-8');
    $pass = htmlspecialchars(trim($_POST['password3']), ENT_QUOTES, 'UTF-8');
    
    // AMAN: Prepared Statement + Filter
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$user, $pass]);
    
    if($stmt->rowCount() > 0) {
        $message3 = "✅ Login BERHASIL! (Prepared Statement + Filter)";
    } else {
        $message3 = "❌ Login GAGAL! (Prepared Statement + Filter)";
    }
}

// Ambil data users untuk ditampilkan
try {
    $users = $pdo->query("SELECT * FROM users ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $users = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection Demo - Keamanan Database</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🔐 SQL Injection Security Demo</h1>
        <p class="subtitle">Demonstrasi Keamanan Database - Tujuan Edukasi</p>

        <div class="warning-box">
            <h3>⚠️ PERINGATAN PENTING</h3>
            <p>Ini adalah demonstrasi untuk tujuan edukasi. <strong>JANGAN</strong> gunakan kode vulnerable di production!</p>
        </div>

        <!-- FORM 1: VULNERABLE -->
        <div class="form-section vulnerable">
            <h2>🔓 Form 1: Login VULNERABLE</h2>
            <p class="desc">Tanpa Prepared Statement & Filter Input - Bisa Inject Query Langsung!</p>
            <form method="POST">
                <input type="text" name="username1" placeholder="Username" required>
                <input type="text" name="password1" placeholder="Password (bisa dikosongkan)">
                <button type="submit" name="login1">Login</button>
            </form>
            <?php if($message1): ?>
                <div class="message"><?php echo $message1; ?></div>
            <?php endif; ?>
            
            <div class="hint-box">
                <strong>💡 Teknik SQL Injection - Coba ini di Username:</strong>
                
                <div class="injection-example">
                    <h4>1️⃣ Bypass Login (tanpa password):</h4>
                    <ul>
                        <li><code>admin' OR '1'='1</code> (password boleh kosong)</li>
                        <li><code>' OR 1=1 -- </code> (-- adalah comment SQL)</li>
                        <li><code>admin'--</code> (login sebagai admin tanpa password)</li>
                    </ul>
                </div>

                <div class="injection-example">
                    <h4>2️⃣ INSERT Data Baru (Union-based):</h4>
                    <ul>
                        <li><code>'; INSERT INTO users VALUES (99,'hacker','pass123','hack@evil.com'); -- </code></li>
                        <li><code>admin'; INSERT INTO users (username,password,email) VALUES ('injected','hacked','evil@test.com'); -- </code></li>
                    </ul>
                </div>

                <div class="injection-example">
                    <h4>3️⃣ UPDATE Data:</h4>
                    <ul>
                        <li><code>'; UPDATE users SET password='hacked' WHERE username='admin'; -- </code></li>
                        <li><code>admin'; UPDATE users SET email='hacked@evil.com' WHERE id=1; -- </code></li>
                    </ul>
                </div>

                <div class="injection-example">
                    <h4>4️⃣ DELETE Data:</h4>
                    <ul>
                        <li><code>'; DELETE FROM users WHERE username='user1'; -- </code></li>
                        <li><code>admin'; DELETE FROM users WHERE id=3; -- </code></li>
                    </ul>
                </div>

                <div class="injection-example danger">
                    <h4>💀 DROP TABLE (BAHAYA!):</h4>
                    <ul>
                        <li><code>'; DROP TABLE users; -- </code> (HATI-HATI: ini menghapus seluruh tabel!)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FORM 2: Prepared Statement -->
        <div class="form-section protected">
            <h2>🔒 Form 2: Login dengan Prepared Statement</h2>
            <p class="desc">Menggunakan Prepared Statement</p>
            <form method="POST">
                <input type="text" name="username2" placeholder="Username" required>
                <input type="text" name="password2" placeholder="Password" required>
                <button type="submit" name="login2">Login</button>
            </form>
            <?php if($message2): ?>
                <div class="message"><?php echo $message2; ?></div>
            <?php endif; ?>
            <div class="hint-box success">
                <strong>✅ Keamanan:</strong> 
                <p>Coba gunakan teknik SQL Injection yang sama seperti Form 1. Semua akan gagal karena:</p>
                <ul>
                    <li>Query dan data dipisahkan</li>
                    <li>Input diperlakukan sebagai data literal, bukan bagian dari query</li>
                    <li>Karakter khusus di-escape otomatis</li>
                </ul>
            </div>
        </div>

        <!-- FORM 3: Prepared Statement + Filter -->
        <div class="form-section protected">
            <h2>🛡️ Form 3: Login dengan Prepared Statement + Filter</h2>
            <p class="desc">Double Protection: Prepared Statement + Input Sanitization</p>
            <form method="POST">
                <input type="text" name="username3" placeholder="Username" required>
                <input type="text" name="password3" placeholder="Password" required>
                <button type="submit" name="login3">Login</button>
            </form>
            <?php if($message3): ?>
                <div class="message"><?php echo $message3; ?></div>
            <?php endif; ?>
            <div class="hint-box success">
                <strong>✅ Keamanan Maksimal:</strong>
                <p>Kombinasi terbaik untuk mencegah SQL Injection:</p>
                <ul>
                    <li>Prepared Statement (proteksi level database)</li>
                    <li>htmlspecialchars() untuk sanitasi input</li>
                    <li>trim() untuk membersihkan whitespace</li>
                </ul>
            </div>
        </div>

        <!-- Tabel Data Users -->
        <div class="table-section">
            <h2>📊 Data Users di Database (Real-time)</h2>
            <p style="text-align: center; color: #718096; margin-bottom: 15px;">
                Tabel ini akan otomatis update setelah SQL Injection berhasil
            </p>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($users) > 0): ?>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['password']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #e53e3e; font-weight: bold;">
                                ⚠️ TABEL KOSONG / TELAH DIHAPUS!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 20px; padding: 15px; background: #fff5f5; border-radius: 8px; border-left: 4px solid #fc8181;">
                <strong>🔄 Reset Database:</strong> 
                <p style="margin-top: 10px;">Jika tabel rusak/terhapus, jalankan ulang SQL setup di bawah</p>
            </div>
        </div>

        <!-- Informasi Setup Database -->
        <div class="info-box">
            <h3>📝 Setup Database</h3>
            <p>Jalankan SQL berikut di MySQL/MariaDB:</p>
            <pre>
CREATE DATABASE IF NOT EXISTS sql_injection_demo;
USE sql_injection_demo;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    email VARCHAR(100)
);

INSERT INTO users (username, password, email) VALUES
('admin', 'admin123', 'admin@test.com'),
('user1', 'pass123', 'user1@test.com'),
('user2', 'pass456', 'user2@test.com');
            </pre>
        </div>

        <!-- Penjelasan Teknis -->
        <div class="explanation-box">
            <h3>🎓 Penjelasan Teknis SQL Injection</h3>
            
            <div class="tech-section">
                <h4>Bagaimana SQL Injection Bekerja?</h4>
                <p><strong>Query Normal:</strong></p>
                <pre>SELECT * FROM users WHERE username='admin' AND password='admin123'</pre>
                
                <p><strong>Query Setelah Injection (input: admin' OR '1'='1):</strong></p>
                <pre>SELECT * FROM users WHERE username='admin' OR '1'='1' AND password='...'</pre>
                <p>Karena '1'='1' selalu TRUE, query mengembalikan semua data!</p>
            </div>

            <div class="tech-section">
                <h4>Mengapa Prepared Statement Aman?</h4>
                <p>Dengan Prepared Statement, input diperlakukan sebagai VALUE, bukan bagian dari QUERY:</p>
                <pre>Query Template: SELECT * FROM users WHERE username = ? AND password = ?
Value 1: "admin' OR '1'='1"  ← Diperlakukan sebagai STRING LITERAL
Value 2: "password"</pre>
                <p>Database tidak akan mengeksekusi 'OR '1'='1' sebagai kondisi SQL!</p>
            </div>
        </div>

        <!-- Kesimpulan -->
        <div class="conclusion-box">
            <h3>📚 Kesimpulan</h3>
            <ol>
                <li><strong>Form 1 (VULNERABLE):</strong> 
                    <ul>
                        <li>✅ Bisa bypass login tanpa password</li>
                        <li>✅ Bisa INSERT data baru via username field</li>
                        <li>✅ Bisa UPDATE data existing</li>
                        <li>✅ Bisa DELETE data</li>
                        <li>✅ Bisa DROP TABLE (menghancurkan database!)</li>
                    </ul>
                </li>
                <li><strong>Form 2 (Prepared Statement):</strong> 
                    <ul>
                        <li>❌ SEMUA teknik injection GAGAL</li>
                        <li>✅ Input diperlakukan sebagai data literal</li>
                    </ul>
                </li>
                <li><strong>Form 3 (Prepared Statement + Filter):</strong> 
                    <ul>
                        <li>❌ SEMUA teknik injection GAGAL</li>
                        <li>✅ Keamanan berlapis (defense in depth)</li>
                    </ul>
                </li>
            </ol>
            
            <p><strong>🎯 Best Practice:</strong> 
                <br>1. Gunakan Prepared Statement (WAJIB!)
                <br>2. Validasi & sanitasi input
                <br>3. Principle of Least Privilege (batasi hak akses database)
                <br>4. Jangan tampilkan error SQL ke user
                <br>5. Gunakan ORM (Laravel, Django) untuk keamanan otomatis
            </p>
        </div>
    </div>
</body>
</html>