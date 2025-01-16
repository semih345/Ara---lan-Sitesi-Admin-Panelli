<?php
require_once "config.php";
include "header.php";

// Arama sorgusunu al
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

// Sayfa numarasını al
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$results_per_page = 12; // Her sayfada gösterilecek sonuç sayısı
$offset = ($page - 1) * $results_per_page;

// Toplam sonuç sayısını al
$count_query = "SELECT COUNT(*) as total FROM ads WHERE title LIKE ? OR description LIKE ?";
$stmt = $conn->prepare($count_query);
$search_param = "%$search%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$total_results = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_results / $results_per_page);

// Arama sorgusunu hazırla
$query = "SELECT ads.*, users.username FROM ads JOIN users ON ads.user_id = users.id WHERE ads.title LIKE ? OR ads.description LIKE ? ORDER BY ads.created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssii", $search_param, $search_param, $results_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1>Arama Sonuçları</h1>

<div class="search-bar">
    <form action="search.php" method="GET">
        <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="İlan ara...">
        <button type="submit">Ara</button>
    </form>
</div>

<p>Aranan: "<?php echo htmlspecialchars($search); ?>" - Toplam <?php echo $total_results; ?> sonuç bulundu.</p>

<div class="ad-grid">
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="ad-card">
            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
            <h2><?php echo htmlspecialchars($row['title']); ?></h2>
            <p class="price"><?php echo number_format($row['price'], 0, ',', '.'); ?> TL</p>
            <p class="status">Durum: <?php echo htmlspecialchars($row['status']); ?></p>
            <p class="owner">Sahibi: <?php echo htmlspecialchars($row['username']); ?></p>
            <a href="ilan-detay.php?id=<?php echo $row['id']; ?>" class="btn">Detaylar</a>
        </div>
    <?php endwhile; ?>
</div>

<?php if ($total_pages > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?q=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>" <?php echo ($page == $i) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php
include "footer.php";
$stmt->close();
$conn->close();
?>

