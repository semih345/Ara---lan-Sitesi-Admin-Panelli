<?php
require_once "config.php";
include "header.php";

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: index.php");
    exit;
}

$username = $password = "";
$username_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Lütfen kullanıcı adınızı girin.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    if (empty(trim($_POST["password"]))) {
        $password_err = "Lütfen şifrenizi girin.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password, is_admin FROM users WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username, $hashed_password, $is_admin);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            
                            $_SESSION['loggedin'] = true;
                            $_SESSION['id'] = $id;
                            $_SESSION['username'] = $username;
                            $_SESSION['is_admin'] = $is_admin;
                            
                            header("location: index.php");
                        } else {
                            $password_err = "Girdiğiniz şifre yanlış.";
                        }
                    }
                } else {
                    $username_err = "Bu kullanıcı adına sahip bir hesap bulunamadı.";
                }
            } else {
                echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }
            $stmt->close();
        }
    }
}
?>

<h1>Giriş Yap</h1>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div>
        <label for="username">Kullanıcı Adı</label>
        <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
        <span class="error"><?php echo $username_err; ?></span>
    </div>    
    <div>
        <label for="password">Şifre</label>
        <input type="password" id="password" name="password" required>
        <span class="error"><?php echo $password_err; ?></span>
    </div>
    <div>
        <button type="submit">Giriş Yap</button>
    </div>
    <p>Hesabınız yok mu? <a href="kayit.php">Şimdi kaydolun</a>.</p>
</form>

<?php
include "footer.php";
?>

