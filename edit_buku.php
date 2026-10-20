
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION["lang"])) {
    $_SESSION["lang"] = "id";
}

$en = $_SESSION["lang"] == "en";

require "koneksi.php";

$error = "";
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM buku WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id]);
$buku = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$buku) {
    header("Location: index.php");
    exit;
}

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
            $sql = "UPDATE buku SET
                    kode_buku = :kode_buku,
                    judul = :judul,
                    penulis = :penulis,
                    penerbit = :penerbit,
                    tahun_terbit = :tahun_terbit,
                    stok = :stok
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":kode_buku" => $kode_buku,
                ":judul" => $judul,
                ":penulis" => $penulis,
                ":penerbit" => $penerbit,
                ":tahun_terbit" => $tahun_terbit,
                ":stok" => $stok,
                ":id" => $id
            ]);

            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == "23000") {
                $error = $en
                    ? "This book code is already in use."
                    : "Kode buku sudah digunakan oleh buku lain.";
            } else {
                $error = $en
                    ? "An error occurred while updating the book."
                    : "Terjadi kesalahan saat mengubah data buku.";
            }
        }
    }

    // Mempertahankan input yang dikirim pengguna
    $buku["kode_buku"] = $kode_buku;
    $buku["judul"] = $judul;
    $buku["penulis"] = $penulis;
    $buku["penerbit"] = $penerbit;
    $buku["tahun_terbit"] = $tahun_terbit;
    $buku["stok"] = $stok;
}
?>

<!DOCTYPE html>
<html lang="<?= $en ? 'en' : 'id' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $en ? "Edit Book" : "Edit Buku" ?></title>
</head>
<body>
    <p>
        <a href="edit_buku.php?id=<?= (int)$id ?>&lang=id">Indonesia</a> |
        <a href="edit_buku.php?id=<?= (int)$id ?>&lang=en">English</a>
    </p>

    <h1><?= $en ? "Edit Book" : "Edit Buku" ?></h1>

    <?php if ($error != ""): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="edit_buku.php?id=<?= (int)$id ?>">
        <label><?= $en ? "Book Code:" : "Kode Buku:" ?></label><br>
        <input type="text" name="kode_buku"
            value="<?= htmlspecialchars((string)$buku["kode_buku"]) ?>"
            required>
        <br><br>

        <label><?= $en ? "Book Title:" : "Judul Buku:" ?></label><br>
        <input type="text" name="judul"
            value="<?= htmlspecialchars((string)$buku["judul"]) ?>"
            required>
        <br><br>

        <label><?= $en ? "Author:" : "Penulis:" ?></label><br>
        <input type="text" name="penulis"
            value="<?= htmlspecialchars((string)$buku["penulis"]) ?>"
            required>
        <br><br>

        <label><?= $en ? "Publisher:" : "Penerbit:" ?></label><br>
        <input type="text" name="penerbit"
            value="<?= htmlspecialchars((string)$buku["penerbit"]) ?>"
            required>
        <br><br>

        <label><?= $en ? "Publication Year:" : "Tahun Terbit:" ?></label><br>
        <input type="number" name="tahun_terbit"
            min="1901" max="<?= date('Y') ?>"
            value="<?= htmlspecialchars((string)$buku["tahun_terbit"]) ?>"
            required>
        <br><br>

        <label><?= $en ? "Stock:" : "Stok:" ?></label><br>
        <input type="number" name="stok" min="0"
            value="<?= htmlspecialchars((string)$buku["stok"]) ?>"
            required>
        <br><br>

        <button type="submit">
            <?= $en ? "Save Changes" : "Simpan Perubahan" ?>
        </button>
    </form>

    <p>
        <a href="index.php">
            <?= $en ? "Back to Catalog" : "Kembali ke Katalog" ?>
        </a>
    </p>
</body>
</html>
