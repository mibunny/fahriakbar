<?php
header('Content-Type: application/json');
require "config/db.php";

$input = file_get_contents("php://input");
$data  = json_decode($input, true);

$uid  = isset($data['uid']) ? $data['uid'] : '';

$response = [];

if($uid == ''){
    $response['status'] = "ERROR";
    $response['message'] = "UID kosong";
    echo json_encode($response);
    exit;
}

$sql = "SELECT * FROM penerima 
        WHERE uid_rfid='$uid' 
        LIMIT 1";

$result = $conn->query($sql);

if($result && $result->num_rows > 0){
    $row = $result->fetch_assoc();

    $response['status']   = "OK";
    $response['nama']     = $row['nama'];
    $response['eligible'] = $row['eligible'];
    $response['kuota']    = intval($row['koutaBantuan']);
    $response['golongan'] = $row['golongan'];
} else {
    $response['status'] = "TIDAK_TERDAFTAR";
}

echo json_encode($response, JSON_PRETTY_PRINT);
$conn->close();
?>
