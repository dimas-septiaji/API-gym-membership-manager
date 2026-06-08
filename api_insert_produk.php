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

$nama_produk = $_POST['nama_produk'] ?? '';
$kategori = $_POST['kategori'] ?? '';
$harga = $_POST['harga'] ?? '';
$stok = $_POST['stok'] ?? '';

if (
    empty($nama_produk) ||
    empty($kategori) ||
    $harga === '' ||
    $stok === ''
) {
    echo json_encode([
        "status" => false,
        "message" => "Semua data wajib diisi"
    ]);
    exit;
}

$query = "
INSERT INTO produk
(
    nama_produk,
    kategori,
    harga,
    stok
)
VALUES
(
    ?, ?, ?, ?
)
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "ssdi",
    $nama_produk,
    $kategori,
    $harga,
    $stok
);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "status" => true,
        "message" => "Produk berhasil ditambahkan"
    ]);

} else {

    echo json_encode([
        "status" => false,
        "message" => mysqli_error($conn)
    ]);

}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>