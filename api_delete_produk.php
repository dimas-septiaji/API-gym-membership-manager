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

if (empty($id)) {

    echo json_encode([
        "status" => false,
        "message" => "ID produk tidak boleh kosong"
    ]);

    exit;
}

$query = "DELETE FROM produk WHERE id = ?";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "status" => true,
        "message" => "Produk berhasil dihapus"
    ]);

} else {

    echo json_encode([
        "status" => false,
        "message" => "Produk gagal dihapus"
    ]);

}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>