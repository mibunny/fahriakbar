<?php

$host = "sql303.infinityfree.com";
$user = "if0_42971248";
$pass = "";
$db   = "if0_42971248_bansos";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode([
        'status' => 'error',
        'msg' => 'Koneksi gagal: ' . $conn->connect_error
    ]));
}

$conn->set_charset("utf8mb4");