<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "config.php";

// Admin girişi kontrolü
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin.php");
    exit();
}

// Veritabanı bağlantısı
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız oldu: " . $conn->connect_error);
}

$id = $title = $description = $price = $status = "";
$id_err = $title_err = $description_err = $price_err = $status_err = "";

// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ID doğrulama
    $id = trim($_POST["id"]);
    if (empty($id) || !is_numeric($id)) {
        $id_err = "Geçersiz ilan ID'si.";
    }

    // Başlık doğrulama
    $title = trim($_POST["title"]);
    if (empty($title)) {
        $title_err = "Lütfen bir başlık girin.";
    }

    // Açıklama doğrulama
    $description = trim($_POST["description"]);
    if (empty($description)) {
        $description_err = "Lütfen bir açıklama girin.";
    }

    // Fiyat doğrulama
    $price = trim($_POST["price"]);
    if (empty($price) || !is_numeric($price) || $price <= 0) {
        $price_err = "Lütfen geçerli bir fiyat girin.";
    }

    // Durum doğrulama
    $status = trim($_POST["status"]);
    if (empty($status) || !in_array($status, ['Aktif', 'Pasif'])) {
        $status_err = "Lütfen geçerli bir durum seçin.";
    }

    // Hata yoksa güncelle
    if (empty($id_err) && empty($title_err) && empty($description_err) && empty($price_err) && empty($status_err)) {
        $sql = "UPDATE ads SET title = ?, description = ?, price = ?, status = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssdsi", $param_title, $param_description, $param_price, $param_status, $param_id);
            
            $param_title = $title;
            $param_description = $description;
            $param_price = $price;
            $param_status = $status;
            $param_id = $id;
            
            if ($stmt->execute()) {
                $_SESSION['admin_success'] = "İlan başarıyla güncellendi.";
                header("location: admin.php");
                exit();
            } else {
                echo "Bir şeyler yanlış gitti. Lütfen daha sonra tekrar deneyin.";
            }
            $stmt->close();
        }
    }
} else {
    // GET isteği - formu göster
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id = trim($_GET["id"]);
        $sql = "SELECT * FROM ads WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $param_id);
            $param_id = $id;
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows == 1) {
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $title = $row["title"];
                    $description = $row["description"];
                    $price = $row["price"];
                    $status = $row["status"];
                } else {
                    $_SESSION['admin_error'] = "Geçersiz ilan ID'si.";
                    header("location: admin.php");
                    exit();
                }
            } else {
                echo "Bir şeyler yanlış gitti. Lütfen daha sonra tekrar deneyin.";
            }
            $stmt->close();
        }
    } else {
        $_SESSION['admin_error'] = "Geçersiz ilan ID'si.";
        header("location: admin.php");
        exit();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İlan Düzenle - Admin Paneli</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>İlan Düzenle</h2>
        <p><a href="admin.php">Admin Paneline Dön</a></p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div>
                <label for="title">Başlık:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                <span class="error"><?php echo $title_err; ?></span>
            </div>
            <div>
                <label for="description">Açıklama:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($description); ?></textarea>
                <span class="error"><?php echo $description_err; ?></span>
            </div>
            <div>
                <label for="price">Fiyat:</label>
                <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" required>
                <span class="error"><?php echo $price_err; ?></span>
            </div>
            <div>
                <label for="status">Durum:</label>
                <select id="status" name="status">
                    <option value="Aktif" <?php echo ($status == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                    <option value="Pasif" <?php echo ($status == 'Pasif') ? 'selected' : ''; ?>>Pasif</option>
                </select>
                <span class="error"><?php echo $status_err; ?></span>
            </div>
            <div>
                <input type="submit" value="İlanı Güncelle">
            </div>
        </form>
    </div>
</body>
</html>