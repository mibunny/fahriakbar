<?php
header("Content-Type: application/json");

if (isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        file_put_contents($path, '{}'); // reset ke kosong
        echo json_encode(['status' => 'ok', 'file' => $file]);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'File tidak ditemukan']);
    }
} else {
    echo json_encode(['status' => 'error', 'msg' => 'No file param']);
}
