<?php
include "config/db.php";

// Ambil finger_id terakhir dari tabel penerima
$result = $conn->query("SELECT MAX(finger_id) AS last FROM penerima");
$row = $result->fetch_assoc();

// Pastikan last tidak NULL (misal belum ada data)
$last_id = isset($row['last']) ? (int)$row['last'] : 0;

// Hitung ID baru
$new_id = $last_id + 1;

// Kirim ID baru ke ESP32 dalam format JSON
header('Content-Type: application/json');
echo json_encode(array("fid" => $new_id));
?>
