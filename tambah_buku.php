
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET["lang"])) {
    if ($_GET["lang"] == "id") {
        $_SESSION["lang"] = "id";
    } elseif ($_GET["lang"] == "en") {
        $_SESSION["lang"] = "en";
    }
}

if (!isset($_SESSION["lang"])) {
    $_SESSION["lang"] = "id";
}

$en = $_SESSION["lang"] == "en";

require "koneksi.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_buku = trim($_POST["kode_buku"] ?? "");
    $judul = trim($_POST["judul"] ?? "");
    $penulis = trim($_POST["penulis"] ?? "");
    $penerbit = trim($_POST["penerbit"] ?? "");
    $tahun_terbit = trim($_POST["tahun_terbit"] ?? "");
    $stok = trim($_POST["stok"] ?? "");

    if (
        $kode_buku == "" || $judul == "" ||
        $penulis == "" || $penerbit == "" ||
        $tahun_terbit == "" || $stok == ""
    ) {
        $error = $en
            ? "All fields are required."
            : "Semua kolom wajib diisi.";
    } elseif (
        !ctype_digit($tahun_terbit) ||
        (int)$tahun_terbit < 1901 ||
        (int)$tahun_terbit > (int)date("Y")
    ) {
        $error = $en
            ? "Invalid publication year."
            : "Tahun terbit tidak valid.";
    } elseif (!ctype_digit($stok)) {
        $error = $en
            ? "Stock must be a non-negative integer."
            : "Stok harus berupa bilangan bulat nonnegatif.";
    } else {
        try {
            $sql = "INSERT INTO buku
                    (kode_buku, judul, penulis, penerbit, tahun_terbit, stok)
                    VALUES
                    (:kode_buku, :judul, :penulis, :penerbit, :tahun_terbit, :stok)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":kode_buku" => $kode_buku,
                ":judul" => $judul,
                ":penulis" => $penulis,
                ":penerbit" => $penerbit,
                ":tahun_terbit" => $tahun_terbit,
                ":stok" => $stok
            ]);

            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == "23000") {
                $error = $en
                    ? "This book code is already in use."
                    : "Kode buku sudah digunakan.";
            } else {
                $error = $en
                    ? "An error occurred while saving the book."
                    : "Terjadi kesalahan saat menyimpan buku.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $en ? 'en' : 'id' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $en ? "Add Book" : "Tambah Buku" ?></title>
</head>
<body>
    <p>
        <a href="tambah_buku.php?lang=id">Indonesia</a> |
        <a href="tambah_buku.php?lang=en">English</a>
    </p>

    <h1><?= $en ? "Add Book" : "Tambah Buku" ?></h1>

    <?php if ($error != ""): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="tambah_buku.php">
        <label><?= $en ? "Book Code:" : "Kode Buku:" ?></label><br>
        <input type="text" name="kode_buku" required>
        <br><br>

        <label><?= $en ? "Book Title:" : "Judul Buku:" ?></label><br>
        <input type="text" name="judul" required>
        <br><br>

        <label><?= $en ? "Author:" : "Penulis:" ?></label><br>
        <input type="text" name="penulis" required>
        <br><br>

        <label><?= $en ? "Publisher:" : "Penerbit:" ?></label><br>
        <input type="text" name="penerbit" required>
        <br><br>

        <label><?= $en ? "Publication Year:" : "Tahun Terbit:" ?></label><br>
        <input type="number" name="tahun_terbit"
               min="1901" max="<?= date('Y') ?>" required>
        <br><br>

        <label><?= $en ? "Stock:" : "Stok:" ?></label><br>
        <input type="number" name="stok" min="0" required>
        <br><br>

        <button type="submit">
            <?= $en ? "Save Book" : "Simpan Buku" ?>
        </button>
    </form>

    <p>
        <a href="index.php">
            <?= $en ? "Back to Catalog" : "Kembali ke Katalog" ?>
        </a>
    </p>
</body>
</html>
