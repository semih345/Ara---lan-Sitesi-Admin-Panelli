<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "config.php";

// Admin girişi kontrolü
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Veritabanı bağlantısı
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız oldu: " . $conn->connect_error);
}

// İlanları getir
$sql = "SELECT * FROM ads ORDER BY created_at DESC";
$result = $conn->query($sql);

// Kullanıcıları listele
$sql_users = "SELECT * FROM users ORDER BY id DESC";
$result_users = $conn->query($sql_users);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - DLD Araç Satış</title>
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
                    <li><a href="index.php">Ana Sayfa</a></li>
                    <li><a href="admin_ilan_ekle.php">Yeni İlan Ekle</a></li>
                    <li><a href="logout.php">Çıkış Yap</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <h2>İlanlar</h2>
            <?php
            // Hata ve başarı mesajlarını göster
            if (isset($_SESSION['admin_success'])) {
                echo '<p class="success">' . htmlspecialchars($_SESSION['admin_success'], ENT_QUOTES, 'UTF-8') . '</p>';
                unset($_SESSION['admin_success']);
            }
            if (isset($_SESSION['admin_error'])) {
                echo '<p class="error">' . htmlspecialchars($_SESSION['admin_error'], ENT_QUOTES, 'UTF-8') . '</p>';
                unset($_SESSION['admin_error']);
            }
            ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Başlık</th>
                        <th>Fiyat</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= number_format($row['price'], 0, ',', '.') ?> TL</td>
                                <td><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <a href="admin_ilan_duzenle.php?id=<?= $row['id'] ?>" class="btn btn-small">Düzenle</a>
                                    <a href="admin_ilan_sil.php?id=<?= $row['id'] ?>" class="btn btn-small btn-danger" onclick="return confirm('Bu ilanı silmek istediğinizden emin misiniz?');">Sil</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">Henüz ilan bulunmamaktadır.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if ($result_users->num_rows > 0): ?>
                <h2>Kullanıcı Yönetimi</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kullanıcı Adı</th>
                            <th>Üyelik Türü</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_users->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= isPremium($row['id']) ? "Premium" : "Standart" ?></td>
                                <td>
                                    <a href="admin_kullanici_duzenle.php?id=<?= $row['id'] ?>" class="btn btn-small">Düzenle</a>
                                    <a href="admin_kullanici_sil.php?id=<?= $row['id'] ?>" class="btn btn-small btn-danger" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?');">Sil</a>
                                    <a href="admin_uyelik_degistir.php?id=<?= $row['id'] ?>" class="btn btn-small btn-warning">Üyelik Değiştir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date("Y") ?> DLD Araç Satış. Tüm hakları saklıdır.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
