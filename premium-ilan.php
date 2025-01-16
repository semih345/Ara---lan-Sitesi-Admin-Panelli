<?php
require_once "config.php";
include "header.php";

// Premium ilanları sorgulama
$sql = "SELECT ads.*, users.username FROM ads 
        JOIN users ON ads.user_id = users.id 
        WHERE ads.is_premium = 1 
        ORDER BY ads.created_at DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Sorgu hatası: " . $conn->error);
}

$total_premium_ads = $result->num_rows;
?>

<main class="container">
    <h1>Premium İlanlar</h1>
    <p>Toplam Premium İlan Sayısı: <?php echo $total_premium_ads; ?></p>
    <div class="ad-grid">
        <?php
        $count = 0;
        if ($total_premium_ads > 0) {
            while($row = $result->fetch_assoc()) {
                $count++;
                
                echo "<div class='ad-card'>";
                echo "<h2>İlan #" . $count . ": " . htmlspecialchars($row['title']) . "</h2>";
                if (!empty($row['image'])) {
                    echo "<img src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['title']) . "' style='max-width:300px;'>";
                } else {
                    echo "<p>Resim bulunamadı</p>";
                }
                echo "<p class='price'>Fiyat: " . number_format($row['price'], 0, ',', '.') . " TL</p>";
                echo "<p>Açıklama: " . htmlspecialchars(substr($row['description'], 0, 100)) . "...</p>";
                echo "<p>Satıcı: " . htmlspecialchars($row['username']) . "</p>";
                echo "<a href='ilan-detay.php?id=" . $row['id'] . "' class='btn'>Detaylar</a>";
                echo "</div>";
            }
            echo "<p>Toplam $count ilan gösterildi.</p>";
        } else {
            echo "<p>Henüz premium ilan bulunmamaktadır.</p>";
        }
        ?>
    </div>
</main>

<?php
include "footer.php";
$conn->close();
?>
