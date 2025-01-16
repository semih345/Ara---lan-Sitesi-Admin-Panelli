<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'dld292ccomtr'); // XAMPP için genellikle 'root'tur
define('DB_PASSWORD', '25e8f-3257ce');     // XAMPP için genellikle boştur
define('DB_NAME', 'dld292ccomtr_dld_arac_satis'); // Veritabanınızın adı

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn === false){
    die("HATA: Veritabanına bağlanılamadı. " . mysqli_connect_error());
}

function isPremium($user_id) {
    global $conn;
    $query = "SELECT * FROM users WHERE id = ? AND is_premium = 1 AND premium_expiry > NOW()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
?>
