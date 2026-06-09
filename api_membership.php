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
//ini jobdesk saya

$result = mysqli_query($conn, "SELECT id, nama_paket, durasi_bulan FROM membership ORDER BY durasi_bulan ASC");

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "id"           => (int)$row["id"],
        "nama_paket"   => $row["nama_paket"],
        "durasi_bulan" => (int)$row["durasi_bulan"]
    ];
}

echo json_encode(["status" => true, "data" => $data]);
mysqli_close($conn);
?>