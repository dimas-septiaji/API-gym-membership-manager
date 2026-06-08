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

$query = "
SELECT
    id,
    nama_produk,
    kategori,
    harga,
    stok
FROM produk
ORDER BY nama_produk ASC
";

$result = mysqli_query($conn, $query);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "id" => (int)$row["id"],
        "nama_produk" => $row["nama_produk"],
        "kategori" => $row["kategori"],
        "harga" => (double)$row["harga"],
        "stok" => (int)$row["stok"]
    ];
}

echo json_encode($data);

mysqli_close($conn);

?>