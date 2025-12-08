<?php
header("Content-Type: application/json");
require_once "../../config/database.php";

$sql = "
    SELECT 
        p.id,
        m.nama AS mahasiswa,
        m.nim,
        b.judul AS buku,
        p.status
    FROM peminjaman p
    JOIN mahasiswa m ON p.mahasiswa_id = m.id
    JOIN buku b ON p.buku_id = b.id
    ORDER BY p.id DESC
";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
