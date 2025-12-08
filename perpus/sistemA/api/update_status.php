<?php
header("Content-Type: application/json");
require_once "../../config/database.php";

if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo json_encode(["success" => false, "message" => "Parameter tidak lengkap"]);
    exit;
}

$id = $_POST['id'];
$status = $_POST['status'];

// Ambil status sebelumnya
$result = $conn->query("SELECT status, buku_id FROM peminjaman WHERE id = $id");
$row = $result->fetch_assoc();
$previous_status = $row['status'];
$buku_id = $row['buku_id'];

// Update status peminjaman
$update = $conn->query("UPDATE peminjaman SET status='$status' WHERE id='$id'");

if ($update) {
    // Kurangi stok hanya jika status diubah menjadi "approved" dan sebelumnya bukan "approved"
    if ($status === "approved" && $previous_status !== "approved") {
        $conn->query("UPDATE buku SET stok = stok - 1 WHERE id = $buku_id AND stok > 0");
    }

    // Kembalikan stok jika status diubah dari "approved" ke "rejected"
    if ($status === "rejected" && $previous_status === "approved") {
        $conn->query("UPDATE buku SET stok = stok + 1 WHERE id = $buku_id");
    }

    echo json_encode(["success" => true, "message" => "Status berhasil diperbarui"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal update"]);
}
?>
