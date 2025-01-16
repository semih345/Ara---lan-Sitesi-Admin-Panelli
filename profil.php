<?php
session_start();
require_once "config.php";

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: giris.php");
    exit;
}

$user_id = $_SESSION['id'];
$upload_error = "";
$upload_success = "";

// Profil fotoğrafı yükleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"])) {
    $target_dir = "uploads/profile_pictures/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5 MB
    
    if ($_FILES["profile_picture"]["size"] > $max_size) {
        $upload_error = "Dosya boyutu çok büyük. Maksimum 5 MB olmalıdır.";
    } elseif (!in_array($_FILES["profile_picture"]["type"], $allowed_types)) {
        $upload_error = "Sadece JPG, PNG ve GIF formatları kabul edilmektedir.";
    } else {
        $file_name = uniqid() . "_" . basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $file_name;
        
        // Resmi yükle ve boyutlandır
        $source_image = imagecreatefromstring(file_get_contents($_FILES["profile_picture"]["tmp_name"]));
        $width = imagesx($source_image);
        $height = imagesy($source_image);
        
        $max_size = 100; // Daha küçük boyut
        if ($width > $height) {
            $new_width = $max_size;
            $new_height = floor($height * ($max_size / $width));
        } else {
            $new_height = $max_size;
            $new_width = floor($width * ($max_size / $height));
        }
        
        $resized_image = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($resized_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        
        if (imagejpeg($resized_image, $target_file, 80)) { // Kaliteyi düşürdük
            $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("si", $target_file, $user_id);
                if ($stmt->execute()) {
                    $upload_success = "Profil fotoğrafı başarıyla güncellendi.";
                } else {
                    $upload_error = "Veritabanı güncellenirken bir hata oluştu: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $upload_error = "SQL sorgusu hazırlanırken bir hata oluştu: " . $conn->error;
            }
        } else {
            $upload_error = "Dosya yüklenirken bir hata oluştu.";
        }
        
        imagedestroy($source_image);
        imagedestroy($resized_image);
    }
}

// Kullanıcı bilgilerini getir
$sql = "SELECT * FROM users WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

include "header.php";
?>

<h1>Profilim</h1>
<div class="profile">
    <?php
    if (!empty($upload_error)) {
        echo "<p class='error'>" . htmlspecialchars($upload_error) . "</p>";
    }
    if (!empty($upload_success)) {
        echo "<p class='success'>" . htmlspecialchars($upload_success) . "</p>";
    }
    // Premium üye kontrolü ve gösterimi
    if (isPremium($user_id)): ?>
        <p class="premium-badge">Premium Üye</p>
    <?php endif; ?>
    <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'uploads/profile_pictures/default.png'); ?>" alt="Profil Fotoğrafı" class="profile-picture">
    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div>
            <label for="profile_picture">Profil Fotoğrafı Yükle (Maksimum 5 MB, JPG/PNG/GIF)</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/gif" required>
        </div>
        <button type="submit">Fotoğrafı Güncelle</button>
    </form>
</div>

<h2>İlanlarım</h2>
<div class="ad-grid">
    <?php
    $sql = "SELECT * FROM ads WHERE user_id = ? ORDER BY created_at DESC";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            echo "<div class='ad-card " . ($row['is_premium'] ? 'premium-ad' : '') . "'>";
            echo "<img src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['title']) . "'>";
            echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p class='price'>" . number_format($row['price'], 0, ',', '.') . " TL</p>";
            if ($row['is_premium']) {
                echo "<span class='premium-label'>Premium İlan</span>";
            }
            echo "<a href='ilan-detay.php?id=" . $row['id'] . "' class='btn'>Detaylar</a>";
            echo "</div>";
        }
        $stmt->close();
    }
    ?>
</div>

<?php include "footer.php"; ?>

