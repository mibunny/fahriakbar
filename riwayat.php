<?php
header('Content-Type: application/json');
require "config/db.php";

// ambil input JSON
$input = file_get_contents("php://input");
$data  = json_decode($input, true);

$uid   = $data['uid'] ?? '';
$berat = $data['berat'] ?? 0;

if($uid == '' || $berat <= 0){
    echo json_encode(["status"=>"ERROR","msg"=>"Data tidak lengkap"]);
    exit;
}

// cari id penerima dari UID
$sql = "SELECT id,nama FROM penerima WHERE uid_rfid=? LIMIT 1";
$stmt = $conn->prepare($sql);
if(!$stmt){
    echo json_encode(["status"=>"ERROR","msg"=>"Prepare failed: ".$conn->error]);
    exit;
}
$stmt->bind_param("s", $uid);
$stmt->execute();
$res = $stmt->get_result();

if($res && $res->num_rows>0){
    $row = $res->fetch_assoc();
    $id_penerima = $row['id'];
    $nama = $row['nama'];

    // insert ke tabel transaksi
    $stmt2 = $conn->prepare("INSERT INTO transaksi (penerima_id, jumlah_beras, tanggal) VALUES (?,?,NOW())");
    if(!$stmt2){
        echo json_encode(["status"=>"ERROR","msg"=>"Prepare insert failed: ".$conn->error]);
        exit;
    }
    $stmt2->bind_param("id", $id_penerima, $berat); // i=int, d=decimal/double
    if($stmt2->execute()){
        echo json_encode([
            "status"=>"OK",
            "msg"=>"Riwayat tersimpan",
            "nama"=>$nama,
            "jumlah"=>$berat
        ]);
    } else {
        echo json_encode(["status"=>"ERROR","msg"=>"Insert gagal: ".$stmt2->error]);
    }

    $stmt2->close();
} else {
    echo json_encode(["status"=>"ERROR","msg"=>"Penerima tidak ditemukan"]);
}

$stmt->close();
$conn->close();
?>
