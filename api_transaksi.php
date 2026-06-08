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

date_default_timezone_set('Asia/Jakarta');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['items']) || count($data['items']) == 0) {
    echo json_encode([
        "status" => false,
        "message" => "Tidak ada item penjualan"
    ]);
    exit;
}

mysqli_begin_transaction($conn);

try {
    $total = 0;
    $detailItems = [];

    foreach ($data['items'] as $item) {
        $produk_id = $item['produk_id'];
        $qty = $item['qty'];

        $queryProduk = mysqli_prepare(
            $conn,
            "SELECT id,nama_produk,harga,stok
             FROM produk
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $queryProduk,
            "i",
            $produk_id
        );

        mysqli_stmt_execute($queryProduk);

        // Menggunakan bind_result
        mysqli_stmt_bind_result($queryProduk, $db_id, $db_nama_produk, $db_harga, $db_stok);
        
        $produk = null;
        if (mysqli_stmt_fetch($queryProduk)) {
            $produk = [
                'id' => $db_id,
                'nama_produk' => $db_nama_produk,
                'harga' => $db_harga,
                'stok' => $db_stok
            ];
        }
        
        // Tutup query sebelum lanjut
        mysqli_stmt_close($queryProduk);

        if (!$produk) {
            throw new Exception("Produk tidak ditemukan");
        }

        if ($produk['stok'] < $qty) {
            throw new Exception("Stok {$produk['nama_produk']} tidak mencukupi");
        }

        $harga = $produk['harga'];
        $subtotal = $harga * $qty;

        $total += $subtotal;

        $detailItems[] = [
            'produk_id' => $produk_id,
            'harga' => $harga,
            'qty' => $qty,
            'subtotal' => $subtotal
        ];
    } // Akhir dari foreach (tadi kurung ini yang terhapus)

    $tanggal = date('Y-m-d H:i:s');

    $insertPenjualan = mysqli_prepare(
        $conn,
        "INSERT INTO penjualan (tanggal, total) VALUES (?, ?)"
    );

    mysqli_stmt_bind_param($insertPenjualan, "sd", $tanggal, $total);
    mysqli_stmt_execute($insertPenjualan);
    $penjualan_id = mysqli_insert_id($conn);
    mysqli_stmt_close($insertPenjualan);

    foreach ($detailItems as $item) {
        $insertDetail = mysqli_prepare(
            $conn,
            "INSERT INTO detail_penjualan (penjualan_id, produk_id, harga, qty, subtotal) VALUES (?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $insertDetail,
            "iidid",
            $penjualan_id,
            $item['produk_id'],
            $item['harga'],
            $item['qty'],
            $item['subtotal']
        );

        mysqli_stmt_execute($insertDetail);
        mysqli_stmt_close($insertDetail);

        $updateStok = mysqli_prepare(
            $conn,
            "UPDATE produk SET stok = stok - ? WHERE id = ?"
        );

        mysqli_stmt_bind_param($updateStok, "ii", $item['qty'], $item['produk_id']);
        mysqli_stmt_execute($updateStok);
        mysqli_stmt_close($updateStok);
    }

    mysqli_commit($conn);

    echo json_encode([
        "status" => true,
        "message" => "Transaksi berhasil",
        "penjualan_id" => $penjualan_id,
        "total" => $total
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode([
        "status" => false,
        "message" => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>