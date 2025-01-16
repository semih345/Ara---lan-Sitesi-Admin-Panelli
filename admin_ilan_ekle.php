<?php
session_start();
require_once "config.php";

// Admin girişi kontrolü
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}

$title = $description = $price = $status = "";
$title_err = $description_err = $price_err = $status_err = $image_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Başlık doğrulama
    if (empty(trim($_POST["title"]))) {
        $title_err = "Lütfen bir başlık girin.";
    } else {
        $title = trim($_POST["title"]);
    }
    
    // Açıklama doğrulama
    if (empty(trim($_POST["description"]))) {
        $description_err = "Lütfen bir açıklama girin.";
    } else {
        $description = trim($_POST["description"]);
    }
    
    // Fiyat doğrulama
    if (empty(trim($_POST["price"]))) {
        $price_err = "Lütfen bir fiyat girin.";
    } elseif (!is_numeric(trim($_POST["price"]))) {
        $price_err = "Fiyat sayısal bir değer olmalıdır.";
    } else {
        $price = trim($_POST["price"]);
    }
    
    // Durum doğrulama
    if (empty(trim($_POST["status"]))) {
        $status_err = "Lütfen bir durum seçin.";
    } else {
        $status = trim($_POST["status"]);
    }
    
    // Resim yükleme
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];
    
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $image_err = "Lütfen JPG, JPEG, PNG veya GIF formatında bir dosya seçin.";
        }
    
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $image_err = "Dosya boyutu 5MB'den küçük olmalıdır.";
        }
    
        if (empty($image_err)) {
            $image = $_FILES["image"]["tmp_name"];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $target_file;
            } else {
                $image_err = "Dosya yüklenirken bir hata oluştu.";
            }
        }
    } else {
        $image_err = "Lütfen bir resim seçin.";
    }
    
    // Hata yoksa veritabanına ekle
    if (empty($title_err) && empty($description_err) && empty($price_err) && empty($status_err) && empty($image_err)) {
        $sql = "INSERT INTO ads (title, description, price, status, image) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssdss", $param_title, $param_description, $param_price, $param_status, $param_image);
            
            $param_title = $title;
            $param_description = $description;
            $param_price = $price;
            $param_status = $status;
            $param_image = $image;
            
            if ($stmt->execute()) {
                header("location: admin.php");
                exit();
            } else {
                echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }
            
            $stmt->close();
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni İlan Ekle - Admin Paneli</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1><a href="admin.php">Admin Paneli</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="admin.php">İlanlar</a></li>
                    <li class="current"><a href="admin_ilan_ekle.php">Yeni İlan Ekle</a></li>
                    <li><a href="logout.php">Çıkış Yap</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <h2>Yeni İlan Ekle</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div>
                    <label for="title">Başlık:</label>
                    <input type="text" id="title" name="title" value="<?php echo $title; ?>">
                    <span class="error"><?php echo $title_err; ?></span>
                </div>
                <div>
                    <label for="description">Açıklama:</label>
                    <textarea id="description" name="description"><?php echo $description; ?></textarea>
                    <span class="error"><?php echo $description_err; ?></span>
                </div>
                <div>
                    <label for="price">Fiyat:</label>
                    <input type="text" id="price" name="price" value="<?php echo $price; ?>">
                    <span class="error"><?php echo $price_err; ?></span>
                </div>
                <div>
                    <label for="status">Durum:</label>
                    <select id="status" name="status">
                        <option value="Aktif" <?php if($status == "Aktif") echo "selected"; ?>>Aktif</option>
                        <option value="Pasif" <?php if($status == "Pasif") echo "selected"; ?>>Pasif</option>
                    </select>
                    <span class="error"><?php echo $status_err; ?></span>
                </div>
                <div>
                    <label for="image">Resim:</label>
                    <input type="file" id="image" name="image">
                    <span class="error"><?php echo $image_err; ?></span>
                </div>
                <div>
                    <input type="submit" value="İlan Ekle">
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2023 DLD Araç Satış. Tüm hakları saklıdır.</p>
    </footer>
</body>
</html>

