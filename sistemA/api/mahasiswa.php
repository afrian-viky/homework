<?php
header("Content-Type: application/json");
require_once "../../config/database.php";

$query = $conn->query("SELECT id, nama, nim FROM mahasiswa");

$data = [];
while ($row = $query->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
