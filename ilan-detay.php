<?php
require_once "config.php";
include "header.php";

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $ad_id = trim($_GET['id']);
    
    $sql = "SELECT ads.*, users.username FROM ads JOIN users ON ads.user_id = users.id WHERE ads.id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $ad_id);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
            } else {
                header("location: error.php");
                exit();
            }
        } else {
            echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
        
        $stmt->close();
    }
} else {
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row['title']); ?> - DLD Araç Satış</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <div class="container">
            <h1><?php echo htmlspecialchars($row['title']); ?></h1>
            <div class="ad-detail">
                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                <p class="price"><?php echo number_format($row['price'], 0, ',', '.'); ?> TL</p>
                <p class="owner">Sahibi: <?php echo htmlspecialchars($row['username']); ?></p>
                <h2>Açıklama</h2>
                <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
            </div>
            <a href="index.php" class="btn">Ana Sayfaya Dön</a>
        </div>
    </main>

    <?php include "footer.php"; ?>
</body>
</html>

