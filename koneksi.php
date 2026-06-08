<?php
$host = "sql309.infinityfree.com";
$user = "if0_42074336";
$pass = "rTRYcdyiUq83";
$db   = "if0_42074336_gym_manager";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    header("Content-Type: application/json");
    echo json_encode([
        "status"  => false,
        "message" => "Koneksi gagal: " . mysqli_connect_error()
    ]);
    exit;
}
?>