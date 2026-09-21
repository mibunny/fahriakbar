<?php
ini_set('display_errors', 1);  // sementara ON untuk debug
ini_set('log_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=UTF-8');

function jsonError($msg)
{
    echo json_encode(['status' => 'error', 'msg' => $msg]);
    exit;
}

require_once "config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Invalid request method');
}

// Ambil data POST
$nama       = trim($_POST['nama'] ?? '');
$nik        = trim($_POST['nik'] ?? '');
$hp         = trim($_POST['hp'] ?? '');
$alamat     = trim($_POST['alamat'] ?? '');
$pendapatan = (int)($_POST['pendapatan'] ?? 0);
$pekerjaan  = (int)($_POST['pekerjaan'] ?? 0);
$tanggungan = (int)($_POST['tanggungan'] ?? 0);
$rumah      = (int)($_POST['rumah'] ?? 0);
$saw_score  = (float)($_POST['saw_score'] ?? 0);
$golongan   = trim($_POST['golongan'] ?? 'Tidak Layak');
$kouta      = (int)($_POST['koutaBantuan'] ?? 0);
$uid_rfid   = trim($_POST['uid_rfid'] ?? '');
$finger_id  = trim($_POST['finger_id'] ?? '');
$eligible   = trim($_POST['eligible'] ?? ($golongan !== 'Tidak Layak' ? 'ya' : 'tidak'));

// Validasi sederhana
if (strlen($nik) !== 16 || !ctype_digit($nik)) jsonError('NIK tidak valid');
if ($nama === '' || $hp === '' || $alamat === '') jsonError('Data wajib tidak boleh kosong');

// Insert ke database
$sql = "INSERT INTO penerima
    (nama, nik, hp, alamat, pendapatan, pekerjaan, tanggungan, rumah, saw_score, golongan, koutaBantuan, uid_rfid, finger_id, eligible)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) jsonError('Prepare failed: ' . $conn->error);

if (!$stmt->bind_param(
    "ssssiiiidsisss",   // ✅ sudah sesuai 14 kolom
    $nama,
    $nik,
    $hp,
    $alamat,
    $pendapatan,
    $pekerjaan,
    $tanggungan,
    $rumah,
    $saw_score,
    $golongan,
    $kouta,
    $uid_rfid,
    $finger_id,
    $eligible
)) {
    jsonError('Bind failed: ' . $stmt->error);
}

if (!$stmt->execute()) {
    jsonError('Execute failed: ' . $stmt->error);
}

// === Generate random PIN (4-digit) ===
$pin = str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);

// (Optional) Store to DB if 'pin' column exists
$lastId = $conn->insert_id;
$colCheck = $conn->query("SHOW COLUMNS FROM penerima LIKE 'pin'");
if ($colCheck && $colCheck->num_rows > 0) {
    $upd = $conn->prepare("UPDATE penerima SET pin=? WHERE id=?");
    if ($upd) {
        $upd->bind_param("si", $pin, $lastId);
        $upd->execute();
        $upd->close();
    }
}

// === Kirim WA lewat Fonnte ===
require_once __DIR__ . "/partials/send_wa.php";

// Return PIN ke frontend
echo json_encode(['status' => 'ok', 'pin' => $pin]);

$stmt->close();
$conn->close();
