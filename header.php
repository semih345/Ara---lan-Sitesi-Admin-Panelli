<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DLD Araç Satış</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Dropdown menü için stil */
        .premium-dropdown {
	    color: yellow;
            position: relative;
            display: inline-block;
        }

        .premium-dropdown-content {
            display: none;
            position: absolute;
            background-color: #35424a;
            min-width: 160px;
            z-index: 1;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .premium-dropdown:hover .premium-dropdown-content {
            display: block;
        }

        .premium-dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .premium-dropdown-content a:hover {
            background-color: #f39c12;
        }

        /* Premium Satın Al butonu */
        .premium-buy {
            color: #f39c12;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <a href="index.php">
                    <img src="logo.png" alt="Logo" class="logo" width="150px" height="150px">
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Ana Sayfa</a></li>
                    <li><a href="ilan-ver.php">İlan Ver</a></li>

                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        <!-- Premium üye menüsü -->
                        <?php if (isPremium($_SESSION['id'])): ?>
                            <li class="premium-dropdown">
                                <a href="javascript:void(0)">Premium Özellikler</a>
                                <div class="premium-dropdown-content">
                                    <a href="premium-ilan-ver.php">Premium İlan Ver</a>
                                    <a href="premium-ilan.php">Premium İlanlar</a>
                                </div>
                            </li>
                        <?php else: ?>
                            <!-- Premium Satın Al Linki -->
                            <li><a href="premium-satin-al.php" class="premium-buy">Premium Satın Al</a></li>
                        <?php endif; ?>
                        <li><a href="profil.php">Hesabım</a></li>
                        <li><a href="logout.php">Çıkış Yap</a></li>
                    <?php else: ?>
                        <li><a href="giris.php">Giriş Yap</a></li>
                        <li><a href="kayit.php">Kayıt Ol</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <!-- Ana içerik burada olacak -->
    </main>
</body>
</html>
