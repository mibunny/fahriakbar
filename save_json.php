<?php
header("Content-Type: application/json");

if (isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $path = __DIR__ . '/' . $file;

    $data = file_get_contents("php://input");
    if ($data) {
        file_put_contents($path, $data);
        echo json_encode(["status" => "ok", "file" => $file]);
    } else {
        echo json_encode(["status" => "error", "msg" => "No data received"]);
    }
} else {
    echo json_encode(["status" => "error", "msg" => "No file param"]);
}
