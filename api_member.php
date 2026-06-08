
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ← tambah ini sementara untuk debug
ini_set('display_errors', 1);
error_reporting(E_ALL);
include "koneksi.php";
$query = "
SELECT
    m.id,
    m.nama,
    m.telp,
    m.alamat,
    m.membership_id,
    mp.nama_paket,
    m.tanggal_daftar,
    m.tanggal_expired,
    DATEDIFF(m.tanggal_expired, CURDATE()) AS sisa_hari
FROM member m
LEFT JOIN membership mp
ON m.membership_id = mp.id
ORDER BY m.id DESC
";

$result = mysqli_query($conn, $query);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {

    $status_membership = "Aktif";

    if ($row['sisa_hari'] <= 0) {
        $status_membership = "Expired";
    } elseif ($row['sisa_hari'] <= 7) {
        $status_membership = "Akan Habis";
    }

    $data[] = [
        "id" => (int)$row["id"],
        "nama" => $row["nama"],
        "telp" => $row["telp"],
        "alamat" => $row["alamat"],
        "membership_id" => (int)$row["membership_id"],
        "nama_paket" => $row["nama_paket"],
        "tanggal_daftar" => $row["tanggal_daftar"],
        "tanggal_expired" => $row["tanggal_expired"],
        "sisa_hari" => (int)$row["sisa_hari"],
        "status_membership" => $status_membership
    ];
}

echo json_encode([
    "status" => true,
    "data" => $data
]);

mysqli_close($conn);

?>