<?php
include 'config/db.php';
include 'config/kriteria.php'; // kalau ada mapping tambahan

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Ambil data penerima sebelum dihapus
    $getQuery = "SELECT * FROM penerima WHERE id = ?";
    $stmt = $conn->prepare($getQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $penerima = $stmt->get_result()->fetch_assoc();

    if ($penerima) {
        // Catat log dulu (penerima_id masih valid)
        $aksi   = "delete";
        $detail = "Data penerima '{$penerima['nama']}' telah dihapus";

        $logQuery = "INSERT INTO riwayat_perubahan (penerima_id, aksi, detail) VALUES (?, ?, ?)";
        $stmtLog = $conn->prepare($logQuery);
        $stmtLog->bind_param("iss", $id, $aksi, $detail);
        $stmtLog->execute();

        // Baru hapus penerima
        $deleteQuery = "DELETE FROM penerima WHERE id = ?";
        $stmtDel = $conn->prepare($deleteQuery);
        $stmtDel->bind_param("i", $id);
        $stmtDel->execute();
    }
}

// Balik ke daftar penerima
header("Location: penerima.php");
exit;
