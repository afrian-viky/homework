<?php
$id = $_GET['id'];

$url = "http://localhost/perpus/sistemA/api/update_status.php";

$data = [
    "id" => $id,
    "status" => "approved"
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

header("Location: peminjaman.php");
exit;
?>
