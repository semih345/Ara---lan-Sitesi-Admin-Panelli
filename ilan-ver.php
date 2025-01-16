<?php
require_once "config.php";
include "header.php";

// Create uploads directory if it doesn't exist
$uploadsDir = 'uploads/';
if (!file_exists($uploadsDir) && !is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: giris.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $conn->real_escape_string($_POST['price']);
    
    $target_dir = "uploads/";
    // Generate a unique filename
    $fileName = uniqid() . '_' . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $fileName;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO ads (user_id, title, description, price, image) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("issds", $_SESSION['id'], $title, $description, $price, $target_file);
            
            if ($stmt->execute()) {
                header("location: index.php");
                exit();
            } else {
                echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }
            $stmt->close();
        }
    } else {
        echo "Resim yüklenirken bir hata oluştu. Hata kodu: " . $_FILES["image"]["error"];
    }
}
?>

<h1>Araç İlanı Ver</h1>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    <div>
        <label for="title">İlan Başlığı</label>
        <input type="text" id="title" name="title" required>
    </div>
    <div>
        <label for="description">Araç Açıklaması</label>
        <textarea id="description" name="description" required></textarea>
    </div>
    <div>
        <label for="price">Fiyat</label>
        <input type="number" id="price" name="price" required>
    </div>
    <div>
        <label for="image">Araç Resmi</label>
        <input type="file" id="image" name="image" accept="image/*" required>
    </div>
    <button type="submit">İlanı Yayınla</button>
</form>

<?php
include "footer.php";
?>

