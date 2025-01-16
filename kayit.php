<?php
require_once "config.php";
include "header.php";

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Lütfen bir kullanıcı adı girin.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = trim($_POST["username"]);
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $username_err = "Bu kullanıcı adı zaten alınmış.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }
            $stmt->close();
        }
    }
    
    if (empty(trim($_POST["password"]))) {
        $password_err = "Lütfen bir şifre girin.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Şifre en az 6 karakter olmalıdır.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Lütfen şifrenizi onaylayın.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Şifreler eşleşmiyor.";
        }
    }
    
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
         
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $param_username, $param_password);
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            
            if ($stmt->execute()) {
                header("location: giris.php");
            } else {
                echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }
            $stmt->close();
        }
    }
}
?>

<h1>Kayıt Ol</h1>
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
        <label for="confirm_password">Şifre Tekrar</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <span class="error"><?php echo $confirm_password_err; ?></span>
    </div>
    <div>
        <button type="submit">Kayıt Ol</button>
    </div>
    <p>Zaten bir hesabınız var mı? <a href="giris.php">Buradan giriş yapın</a>.</p>
</form>

<?php
include "footer.php";
?>

