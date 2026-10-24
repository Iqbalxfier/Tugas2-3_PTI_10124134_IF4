<?php
session_start();
require "koneksi.php";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validasi input
    if ($username == "" || $password == "") {
        $error = "Username dan password wajib diisi!!";
    } else {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":username" => $username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["fullname"] = $user["fullname"];
            $_SESSION["lang"] = "id";

            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Katalog Buku</title>
</head>
<body>
    <table align="center">
        <tr>
            <td>
                <h2>Login Katalog Buku</h2>

                <?php if ($error != ""): ?>
                    <p style="color: red;">
                        <?= htmlspecialchars($error) ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <table class="form-table" align="center">
                        <tr>
                            <td>Username : </td>
                            <td><input type="text" name="username" required></td>
                        </tr>
                        <tr>
                            <td>Password : </td>
                            <td><input type="password" name="password" required></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <button type="submit">Login</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </td>
        </tr>
    </table>
</body>
</html>