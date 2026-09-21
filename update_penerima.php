<?php
include 'config/db.php';
include 'config/kriteria.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id         = $_POST['id'];
    $nama       = $_POST['nama'];
    $alamat     = $_POST['alamat'];
    $pendapatan = $_POST['pendapatan'];
    $pekerjaan  = $_POST['pekerjaan'];
    $tanggungan = $_POST['tanggungan'];
    $rumah      = $_POST['rumah'];

    // Ambil data lama
    $oldQuery = "SELECT * FROM penerima WHERE id = ?";
    $stmtOld = $conn->prepare($oldQuery);
    $stmtOld->bind_param("i", $id);
    $stmtOld->execute();
    $oldData = $stmtOld->get_result()->fetch_assoc();

    // Update data penerima
    $query = "UPDATE penerima SET 
                nama = ?, 
                alamat = ?, 
                pendapatan = ?, 
                pekerjaan = ?, 
                tanggungan = ?, 
                rumah = ?
              WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssiiiii", $nama, $alamat, $pendapatan, $pekerjaan, $tanggungan, $rumah, $id);

    if ($stmt->execute()) {
        // Buat detail perubahan
        $perubahan = [];

        if ($oldData['nama'] != $nama) {
            $perubahan[] = "Nama: {$oldData['nama']} ➝ {$nama}";
        }
        if ($oldData['alamat'] != $alamat) {
            $perubahan[] = "Alamat: {$oldData['alamat']} ➝ {$alamat}";
        }
        if ($oldData['pendapatan'] != $pendapatan) {
            $perubahan[] = "Pendapatan: {$opsi['pendapatan'][$oldData['pendapatan']]} ➝ {$opsi['pendapatan'][$pendapatan]}";
        }
        if ($oldData['pekerjaan'] != $pekerjaan) {
            $perubahan[] = "Pekerjaan: {$opsi['pekerjaan'][$oldData['pekerjaan']]} ➝ {$opsi['pekerjaan'][$pekerjaan]}";
        }
        if ($oldData['tanggungan'] != $tanggungan) {
            $perubahan[] = "Tanggungan: {$opsi['tanggungan'][$oldData['tanggungan']]} ➝ {$opsi['tanggungan'][$tanggungan]}";
        }
        if ($oldData['rumah'] != $rumah) {
            $perubahan[] = "Rumah: {$opsi['rumah'][$oldData['rumah']]} ➝ {$opsi['rumah'][$rumah]}";
        }

        if (!empty($perubahan)) {
            $detail = implode(", ", $perubahan);
            $aksi   = "update";

            $logQuery = "INSERT INTO riwayat_perubahan (penerima_id, aksi, detail) VALUES (?, ?, ?)";
            $stmtLog = $conn->prepare($logQuery);
            $stmtLog->bind_param("iss", $id, $aksi, $detail);
            $stmtLog->execute();
        }

        header("Location: penerima.php?status=success");
    } else {
        header("Location: penerima.php?status=error");
    }
    exit;
} else {
    header("Location: penerima.php");
    exit;
}
