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

// GET parametresinden id'yi al ve güvenli hale getir
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    $_SESSION['admin_error'] = "Geçersiz ilan ID'si.";
    header("Location: admin.php");
    exit();
}

// Veritabanı bağlantısı
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız oldu: " . $conn->connect_error);
}

// İlanı sil
$stmt = $conn->prepare("DELETE FROM ads WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['admin_success'] = "İlan başarıyla silindi.";
} else {
    $_SESSION['admin_error'] = "İlan silinirken bir hata oluştu: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Admin paneline geri yönlendir
header("Location: admin.php");
exit();
?>

