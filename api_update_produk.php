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
$nama_produk = $_POST['nama_produk'] ?? '';
$kategori = $_POST['kategori'] ?? '';
$harga = $_POST['harga'] ?? '';
$stok = $_POST['stok'] ?? '';

if (
    empty($id) ||
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
UPDATE produk
SET
    nama_produk = ?,
    kategori = ?,
    harga = ?,
    stok = ?
WHERE id = ?
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "ssdii",
    $nama_produk,
    $kategori,
    $harga,
    $stok,
    $id
);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "status" => true,
        "message" => "Produk berhasil diupdate"
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