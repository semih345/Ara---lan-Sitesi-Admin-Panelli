<?php
require_once "config.php";
include "header.php";

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $user_id = trim($_GET['id']);
    
    // Kullanıcı bilgilerini getir
    $sql = "SELECT * FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if (!$user) {
            header("location: error.php");
            exit();
        }
    } else {
        header("location: error.php");
        exit();
    }
} else {
    header("location: error.php");
    exit();
}
?>

<h1><?php echo htmlspecialchars($user['username']); ?> Profili</h1>
<div class="profile">
    <img src="<?php echo $user['profile_picture'] ?? 'uploads/profile_pictures/default.png'; ?>" alt="Profil Fotoğrafı" class="profile-picture">
    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
</div>

<h2><?php echo htmlspecialchars($user['username']); ?> İlanları</h2>
<div class="ad-grid">
    <?php
    $sql = "SELECT * FROM ads WHERE user_id = ? AND status = 'active' ORDER BY created_at DESC";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            echo "<div class='ad-card'>";
            echo "<img src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['title']) . "'>";
            echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p class='price'>" . number_format($row['price'], 0, ',', '.') . " TL</p>";
            echo "<a href='ilan-detay.php?id=" . $row['id'] . "' class='btn'>Detaylar</a>";
            echo "</div>";
        }
        $stmt->close();
    }
    ?>
</div>

<?php include "footer.php"; ?>

