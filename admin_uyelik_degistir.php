<?php
session_start();
require_once "config.php";

// Admin girişi kontrolü
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $id = trim($_GET['id']);
    
    $sql = "SELECT is_premium FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $is_premium = $row['is_premium'];
                
                // Üyelik türünü değiştir
                $new_status = $is_premium ? 0 : 1;
                $update_sql = "UPDATE users SET is_premium = ?, premium_expiry = ? WHERE id = ?";
                if ($update_stmt = $conn->prepare($update_sql)) {
                    $expiry = $new_status ? date('Y-m-d H:i:s', strtotime('+30 days')) : NULL;
                    $update_stmt->bind_param("isi", $new_status, $expiry, $id);
                    if ($update_stmt->execute()) {
                        header("location: admin.php");
                        exit();
                    } else {
                        echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
                    }
                    $update_stmt->close();
                }
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

$conn->close();
?>

