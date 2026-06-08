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

$id = $_GET['id'] ?? '';

if (empty($id)) {

    echo json_encode([
        "status" => false,
        "message" => "ID penjualan tidak boleh kosong"
    ]);

    exit;
}

$query = "
SELECT
    dp.id,
    dp.penjualan_id,
    dp.produk_id,
    p.nama_produk,
    dp.harga,
    dp.qty,
    dp.subtotal
FROM detail_penjualan dp
INNER JOIN produk p
ON dp.produk_id = p.id
WHERE dp.penjualan_id = ?
ORDER BY dp.id ASC
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_bind_result($stmt, $id_detail, $penjualan_id, $produk_id, $nama_produk, $harga, $qty, $subtotal);
$data = [];
while (mysqli_stmt_fetch($stmt)) {
    $data[] = [
        "id" => (int)$id_detail,
        "penjualan_id" => (int)$penjualan_id,
        "produk_id" => (int)$produk_id,
        "nama_produk" => $nama_produk,
        "harga" => (double)$harga,
        "qty" => (int)$qty,
        "subtotal" => (double)$subtotal
    ];
}

echo json_encode([
    "status" => true,
    "data" => $data
]);

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>