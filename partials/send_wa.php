<?php
// Jangan query database lagi, gunakan data yang sudah ada
if (!isset($nama) || !isset($hp) || !isset($pin)) {
    return; // kalau dipanggil tanpa variabel, langsung stop
}

// pastikan nomor pakai format internasional (62)
$hp = preg_replace('/^0/', '62', $hp);

// isi pesan
$message = "Halo $nama 👋,\n\nPendaftaran Anda berhasil ✅\n\nPIN Anda: *$pin*\n\nSimpan baik-baik PIN ini untuk verifikasi bantuan.";

// token fonnte (ganti dengan token asli)
$token = "E9VwnX5gKtAhhcqdjgDW";

// kirim WA via Fonnte
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.fonnte.com/send",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => array(
        'target' => $hp,
        'message' => $message,
    ),
    CURLOPT_HTTPHEADER => array(
        "Authorization: $token"
    ),
));
$response = curl_exec($curl);
curl_close($curl);

// opsional: kalau mau log / debug
// file_put_contents("wa_log.txt", date("Y-m-d H:i:s")." => $response\n", FILE_APPEND);
