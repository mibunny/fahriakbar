<?php
// t1/api/getData.php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../config/db.php'; // pastikan $conn tersedia

function respond($arr, $code = 200) {
    http_response_code($code);
    echo json_encode($arr);
    exit;
}

// Optional: filter by ?uid=XXXX
$uidParam = isset($_GET['uid']) ? trim($_GET['uid']) : '';
if ($uidParam !== '') {
    $uidParam = strtoupper(preg_replace('/\s+/', '', $uidParam));
    $stmt = $conn->prepare("SELECT uid_rfid, finger_id, koutaBantuan FROM penerima WHERE UPPER(REPLACE(uid_rfid,' ','')) = ? ORDER BY COALESCE(created_at,'1970-01-01') DESC, id DESC LIMIT 1");
    $stmt->bind_param('s', $uidParam);
} else {
    $stmt = $conn->prepare("SELECT uid_rfid, finger_id, koutaBantuan FROM penerima ORDER BY COALESCE(created_at,'1970-01-01') DESC, id DESC LIMIT 1");
}

if (!$stmt || !$stmt->execute()) {
    respond(["error" => "DB error", "uid" => "", "fingerid" => 0, "berat_tutup" => 0], 500);
}

$res = $stmt->get_result();
$row = $res->fetch_assoc();

if ($row) {
    $uid = strtoupper(preg_replace('/\s+/', '', $row['uid_rfid']));
    respond([
        "uid" => $uid,
        "fingerid" => (int)($row['finger_id'] ?? 0),
        "berat_tutup" => (float)($row['koutaBantuan'] ?? 0)
    ]);
} else {
    respond(["uid" => "", "fingerid" => 0, "berat_tutup" => 0]);
}
