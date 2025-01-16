<?php
session_start();
require_once "config.php";

// Admin girişi kontrolü
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$id = $username = $email = "";
$username_err = $email_err = "";

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $id = trim($_GET['id']);
    
    $sql = "SELECT * FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $username = $row['username'];
                $email = $row['email'];
            } else {
                header("location: error.php");
                exit();
            }
        } else {
            echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form verilerini doğrula ve güncelle
    // (Bu kısmı mevcut kayit.php dosyasından uyarlayabilirsiniz)
    
    if (empty($username_err) && empty($email_err)) {
        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssi", $username, $email, $id);
            
            if ($stmt->execute()) {
                header("location: admin.php");
                exit();
            } else {
                echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }

            $stmt->close();
        }
    }
}

include "header.php";
?>

<h1>Kullanıcı Düzenle</h1>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>" method="post">
    <div>
        <label for="username">Kullanıcı Adı</label>
        <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
        <span class="error"><?php echo $username_err; ?></span>
    </div>
    <div>
        <label for="email">E-posta</label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
        <span class="error"><?php echo $email_err; ?></span>
    </div>
    <button type="submit">Güncelle</button>
    <a href="admin.php" class="btn btn-secondary">İptal</a>
</form>

<?php include "footer.php"; ?>
