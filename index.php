<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config.php";
include "header.php";

if (!$conn) {
    die("Veritabanı bağlantısı başarısız oldu: " . mysqli_connect_error());
}

// Sadece premium olmayan ilanları listelemek için sorgu
$sql = "SELECT ads.*, users.username FROM ads 
        JOIN users ON ads.user_id = users.id 
        WHERE ads.is_premium = 0  -- Premium olmayan ilanlar
        ORDER BY ads.created_at DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Sorgu hatası: " . $conn->error);
}

$total_ads = $result->num_rows;
?>

<main>
    <div class="container">
        <h1>Tüm İlanlar</h1>
        <div class="search-bar">
            <form action="search.php" method="GET">
                <input type="text" name="q" placeholder="İlan ara...">
                <button type="submit">Ara</button>
            </form>
        </div>
        <p>Toplam İlan Sayısı: <?php echo $total_ads; ?></p>
        <div class="ad-grid">
            <?php
            $count = 0;
            if ($total_ads > 0) {
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
                echo "<p>Henüz ilan bulunmamaktadır.</p>";
            }
            ?>
        </div>
    </div>
</main>

<?php
include "footer.php";
$conn->close();
ob_end_flush();
?>
