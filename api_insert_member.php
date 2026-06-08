<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include "koneksi.php";

date_default_timezone_set('Asia/Jakarta');

$nama          = $_POST['nama'] ?? '';
$telp          = $_POST['telp'] ?? '';
$alamat        = $_POST['alamat'] ?? '';
$membership_id = $_POST['membership_id'] ?? '';

if (empty($nama) || empty($telp) || empty($alamat) || empty($membership_id)) {
    echo json_encode(["status" => false, "message" => "Semua data wajib diisi"]);
    exit;
}

// Ambil durasi paket — TANPA mysqli_stmt_get_result
$stmtM = mysqli_prepare($conn, "SELECT durasi_bulan FROM membership WHERE id = ?");
mysqli_stmt_bind_param($stmtM, "i", $membership_id);
mysqli_stmt_execute($stmtM);

$durasi_bulan = null;
mysqli_stmt_bind_result($stmtM, $durasi_bulan); // ← pakai bind_result, bukan get_result
mysqli_stmt_fetch($stmtM);
mysqli_stmt_close($stmtM);

if ($durasi_bulan === null) {
    echo json_encode(["status" => false, "message" => "Paket membership tidak ditemukan"]);
    exit;
}

$tanggal_daftar  = date('Y-m-d');
$tanggal_expired = date('Y-m-d', strtotime("+{$durasi_bulan} month"));

$stmt = mysqli_prepare($conn,
    "INSERT INTO member (nama, telp, alamat, membership_id, tanggal_daftar, tanggal_expired)
     VALUES (?, ?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, "sssiss", $nama, $telp, $alamat, $membership_id, $tanggal_daftar, $tanggal_expired);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["status" => true, "message" => "Member berhasil ditambahkan"]);
} else {
    echo json_encode(["status" => false, "message" => "Gagal: " . mysqli_stmt_error($stmt)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>