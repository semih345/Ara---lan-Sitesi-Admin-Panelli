<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Hata mesajlarını göster
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config.php";

// Oturum kontrolü
if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true) {
    header("Location: admin.php");
    exit;
}

// Giriş işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // POST verilerinin varlığını kontrol et
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $error = "Kullanıcı adı ve şifre gereklidir.";
    } else {
        $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if ($conn->connect_error) {
            die("Veritabanı bağlantısı başarısız oldu: " . $conn->connect_error);
        }

        // Karakter kodlaması ayarı
        $conn->set_charset("utf8mb4");

        $sql = "SELECT id, username, password FROM users WHERE username = ? AND is_admin = 1";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $db_username, $hashed_password);
                    if ($stmt->fetch()) {
                        // Hata ayıklama kodu
                        error_log("Giriş denemesi - Kullanıcı: $username");
                        error_log("Hashli şifre (DB): " . $hashed_password);
                        error_log("Girilen şifre: " . $password);
                        error_log("password_verify sonucu: " . (password_verify($password, $hashed_password) ? 'true' : 'false'));

                        if (password_verify($password, $hashed_password)) {
                            $_SESSION['admin_loggedin'] = true;
                            $_SESSION['admin_id'] = $id;
                            $_SESSION['admin_username'] = $db_username;
                            header("location: admin.php");
                            exit;
                        } else {
                            $error = "Geçersiz şifre.";
                        }
                    }
                } else {
                    $error = "Geçersiz kullanıcı adı veya yetkiniz yok.";
                }
            } else {
                $error = "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi - DLD Araç Satış</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Admin Girişi</h2>
        <?php
        if (isset($error)) {
            echo '<p class="error">' . htmlspecialchars($error) . '</p>';
        }
        if (isset($_SESSION['admin_login_error'])) {
            echo '<p class="error">' . htmlspecialchars($_SESSION['admin_login_error']) . '</p>';
            unset($_SESSION['admin_login_error']);
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Giriş Yap" class="btn">
            </div>
        </form>
    </div>
</body>
</html>