<?php
header("Content-Type: application/json");

// koneksi database
$conn = new mysqli("localhost", "root", "", "bansos");
if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi gagal"]));
}

// ambil raw json
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}

$uid      = $data["uid"] ?? null;
$fingerid = $data["fingerid"] ?? null;
$berat    = $data["berat"] ?? 0;

// cek penerima
$stmt = $conn->prepare("SELECT id, koutaBantuan FROM penerima WHERE uid_rfid = ? AND finger_id = ?");
$stmt->bind_param("ss", $uid, $fingerid);
$stmt->execute();
$res = $stmt->get_result();
$penerima = $res->fetch_assoc();

if (!$penerima) {
    echo json_encode(["error" => "Penerima tidak ditemukan"]);
    exit;
}

// cek kouta
if ($penerima["koutaBantuan"] <= 0) {
    echo json_encode(["error" => "Kouta sudah habis"]);
    exit;
}

// simpan transaksi
$stmt = $conn->prepare("INSERT INTO transaksi (penerima_id, jumlah_beras, tanggal) VALUES (?, ?, NOW())");
$stmt->bind_param("ii", $penerima["id"], $berat);
$stmt->execute();

// update kouta
$stmt = $conn->prepare("UPDATE penerima SET koutaBantuan = koutaBantuan - 1 WHERE id = ?");
$stmt->bind_param("i", $penerima["id"]);
$stmt->execute();

echo json_encode([
    "status" => "ok",
    "msg" => "Transaksi tersimpan",
    "penerima_id" => $penerima["id"],
    "sisa_kouta" => $penerima["koutaBantuan"] - 1
]);
