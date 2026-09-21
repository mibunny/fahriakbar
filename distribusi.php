<?php
header('Content-Type: application/json');
require "config/db.php";

$input = file_get_contents("php://input");
$data  = json_decode($input, true);

$uid  = isset($data['uid']) ? $data['uid'] : '';
$fid  = isset($data['fid']) ? $data['fid'] : '';
$pin  = isset($data['pin']) ? $data['pin'] : '';

$response = [];

// validasi input
if($uid == '' || $fid == '' || $pin == ''){
    $response['status'] = "ERROR";
    $response['message'] = "Data tidak lengkap";
    echo json_encode($response);
    exit;
}

// cek database
$sql = "SELECT * FROM penerima 
        WHERE uid_rfid='$uid' 
          AND finger_id='$fid' 
          AND pin='$pin' 
          AND eligible='ya'
        LIMIT 1";

$result = $conn->query($sql);

if($result && $result->num_rows > 0){
    $row = $result->fetch_assoc();

    if($row['koutaBantuan'] > 0){
        // kurangi kuota jadi 0 setelah distribusi
        $upd = "UPDATE penerima SET koutaBantuan=0 WHERE id=".$row['id'];
        $conn->query($upd);

        $response['status']   = "OK";
        $response['target']   = intval($row['koutaBantuan']); // ini nilai kuota sebelum dikurangi
        $response['golongan'] = $row['golongan'];
    } else {
        $response['status'] = "HABIS";
    }

} else {
    $response['status'] = "TIDAK_TERDAFTAR";
}

echo json_encode($response, JSON_PRETTY_PRINT);
$conn->close();
?>
