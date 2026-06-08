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
    tanggal,
    total
FROM penjualan
ORDER BY id DESC
";

$result = mysqli_query($conn, $query);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {

    $data[] = [
        "id" => (int)$row["id"],
        "tanggal" => $row["tanggal"],
        "total" => (double)$row["total"]
    ];
}

echo json_encode([
    "status" => true,
    "data" => $data
]);

mysqli_close($conn);

?>