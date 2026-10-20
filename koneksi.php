<?php
$host = "localhost";
$nama_db = "db_katalog_buku";
$username = "root";
$password = "";

$dsn ="mysql:host=" . $host . ";dbname=" . $nama_db . ";charset=utf8";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>





