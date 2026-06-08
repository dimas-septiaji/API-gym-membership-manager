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

$id = $_POST['id'] ?? '';
$membership_id = $_POST['membership_id'] ?? '';

if (
    empty($id) ||
    empty($membership_id)
) {
    echo json_encode([
        "status" => false,
        "message" => "Data tidak lengkap"
    ]);
    exit;
}

$qPaket = mysqli_prepare(
    $conn,
    "SELECT durasi_bulan
     FROM membership
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $qPaket,
    "i",
    $membership_id
);

mysqli_stmt_execute($qPaket);

$result = mysqli_stmt_get_result($qPaket);
$paket = mysqli_fetch_assoc($result);

if (!$paket) {
    echo json_encode([
        "status" => false,
        "message" => "Membership tidak ditemukan"
    ]);
    exit;
}

$tanggal_daftar = date('Y-m-d');

$tanggal_expired = date(
    'Y-m-d',
    strtotime("+{$paket['durasi_bulan']} month")
);

$query = "
UPDATE member
SET
    membership_id = ?,
    tanggal_expired = ?
WHERE id = ?
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "isi",
    $membership_id,
    $tanggal_expired,
    $id
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => true,
        "message" => "Membership berhasil diperpanjang"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => mysqli_error($conn)
    ]);
}
?>