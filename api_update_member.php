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
$nama = $_POST['nama'] ?? '';
$telp = $_POST['telp'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$membership_id = $_POST['membership_id'] ?? '';

if (
    empty($id) ||
    empty($nama) ||
    empty($telp) ||
    empty($alamat) ||
    empty($membership_id)
) {
    echo json_encode([
        "status" => false,
        "message" => "Semua data wajib diisi"
    ]);
    exit;
}

$queryMembership = "
SELECT durasi_bulan
FROM membership
WHERE id = ?
";

$stmtMembership = mysqli_prepare($conn, $queryMembership);

mysqli_stmt_bind_param(
    $stmtMembership,
    "i",
    $membership_id
);

mysqli_stmt_execute($stmtMembership);

$resultMembership = mysqli_stmt_get_result($stmtMembership);

$membership = mysqli_fetch_assoc($resultMembership);

if (!$membership) {
    echo json_encode([
        "status" => false,
        "message" => "Membership tidak ditemukan"
    ]);
    exit;
}

$durasi_bulan = $membership['durasi_bulan'];

$tanggal_expired = date(
    'Y-m-d',
    strtotime("+{$durasi_bulan} month")
);

$query = "
UPDATE member
SET
    nama = ?,
    telp = ?,
    alamat = ?,
    membership_id = ?,
    tanggal_expired = ?
WHERE id = ?
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "sssisi",
    $nama,
    $telp,
    $alamat,
    $membership_id,
    $tanggal_expired,
    $id
);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "status" => true,
        "message" => "Member berhasil diupdate"
    ]);

} else {

    echo json_encode([
        "status" => false,
        "message" => "Member gagal diupdate"
    ]);

}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>