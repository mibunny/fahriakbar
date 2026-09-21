<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "bansos";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'msg' => 'Koneksi gagal: ' . $conn->connect_error]));
}
