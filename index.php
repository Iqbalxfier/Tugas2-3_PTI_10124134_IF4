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



require "koneksi.php";

// Mengambil semua data buku
$sql = "SELECT * FROM buku ORDER BY id ASC";
$stmt = $pdo->query($sql);
$buku = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SESSION["lang"] == "en") {
    $judul = "Book Catalog";
    $selamat_datang = "Welcome";
    $logout = "Logout";
    $daftar_buku = "Book List";
    $tambah_buku = "Add Book";
} else {
    $judul = "Katalog Buku";
    $selamat_datang = "Selamat datang";
    $logout = "Logout";
    $daftar_buku = "Daftar Buku";
    $tambah_buku = "Tambah Buku";
}

?>


<html>
<html lang="<?= $_SESSION["lang"] == "en" ? "en" : "id" ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title align="center"><?= $judul ?></title>
</head>
<body>

    <header>
        <table width="100%" cellpadding="10" algin="center">
            <tr>
                <td>
                    <h1> <?= $judul ?></h1>
                    <p>
                        <?= $_SESSION["lang"] == "en"
                            ? "Book Management System"
                            : "Sistem Pengelolaan Data Buku" ?>
                    </p>
                </td>
                <td align="right" valign="top">
                    <nav>
                        <a href="index.php?lang=id">Indonesia</a>
                        |
                        <a href="index.php?lang=en">English</a>
                        <br><br>
                        <a href="logout.php"><?= $logout ?></a>
                    </nav>
                </td>
            </tr>
        </table>
    </header>

    <hr>

    <main>
        <section>
            <h2>
                <?= $_SESSION["lang"] == "en"
                    ? "Dashboard"
                    : "Beranda Katalog" ?>
            </h2>

            <p>
                <?= $selamat_datang ?>,
                <strong><?= htmlspecialchars($_SESSION["fullname"]) ?></strong>!
            </p>

        </section>

        <hr>

        <section>
            <table width="100%" cellpadding="8">
                <tr>
                    <td>
                        <h2><?= $daftar_buku ?></h2>
                    </td>
                    <td align="right">
                        <a href="tambah_buku.php">
                            <button type="button">
                                + <?= $tambah_buku ?>
                            </button>
                        </a>
                    </td>
                </tr>
            </table>


            <table border="1" width="100%" cellpadding="10"
                   cellspacing="0">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th><?= $_SESSION["lang"] == "en" ? "Book Code" : "Kode Buku" ?></th>
                        <th><?= $_SESSION["lang"] == "en" ? "Title" : "Judul" ?></th>
                        <th><?= $_SESSION["lang"] == "en" ? "Author" : "Penulis" ?></th>
                        <th><?= $_SESSION["lang"] == "en" ? "Publisher" : "Penerbit" ?></th>
                        <th><?= $_SESSION["lang"] == "en" ? "Publication Year" : "Tahun Terbit" ?></th>
                        <th><?= $_SESSION["lang"] == "en" ? "Stock" : "Stok" ?></th>
                        <th><?= $_SESSION["lang"] == "en" ? "Actions" : "Aksi" ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($buku) > 0): ?>
                        <?php $no = 1; ?>

                        <?php foreach ($buku as $item): ?>
                            <tr>
                                <td align="center"><?= $no++ ?></td>
                                <td><?= htmlspecialchars($item["kode_buku"]) ?></td>
                                <td><?= htmlspecialchars($item["judul"]) ?></td>
                                <td><?= htmlspecialchars($item["penulis"]) ?></td>
                                <td><?= htmlspecialchars($item["penerbit"]) ?></td>
                                <td align="center"><?= htmlspecialchars($item["tahun_terbit"]) ?></td>
                                <td align="center"><?= htmlspecialchars($item["stok"]) ?></td>
                                <td align="center">
                                    <a href="edit_buku.php?id=<?= (int)$item["id"] ?>">
                                        <?= $_SESSION["lang"] == "en" ? "Edit" : "Edit" ?>
                                    </a>

                                    |

                                    <form method="POST" action="hapus_buku.php"
                                          onsubmit="return confirm('<?= $_SESSION["lang"] == "en" ? "Delete this book?" : "Yakin ingin menghapus buku ini?" ?>');"
                                          style="display: inline;">
                                        <input type="hidden" name="id"
                                               value="<?= (int)$item["id"] ?>">
                                        <button type="submit">
                                            <?= $_SESSION["lang"] == "en" ? "Delete" : "Hapus" ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="8" align="center">
                                <?= $_SESSION["lang"] == "en"
                                    ? "No books available."
                                    : "Belum ada data buku." ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>


</body>
</html>
