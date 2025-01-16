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
    <title>Premium Satın Al</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-image: url('arka-plan.jpg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            color: white;
            text-align: center;
        }
        .container {
            margin: 50px auto;
            padding: 30px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            width: 50%;
        }
        .product-image {
            width: 300px;
            height: 300px;
            object-fit: cover;
        }
        .buy-button {
            background-color: #f39c12;
            color: white;
            padding: 10px 20px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        .buy-button:hover {
            background-color: #e67e22;
        }
        .header-nav {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background-color: #35424a;
        }
        .header-nav a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 10px;
        }
        .header-nav a:hover {
            background-color: #f39c12;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header-nav">
        <a href="index.php">Ana Sayfa</a>
    </div>

    <div class="container">
        <h1>Premium Üyelik</h1>
        <p>Özellikler: Premium ilan ekleme</p>
        <img src="premium-image.jpg" alt="Premium Üyelik" class="product-image">
        <p>Fiyat: 150 TL</p>
        <form action="payment.php" method="post">
            <button type="submit" class="buy-button">Premium Satın Al</button>
        </form>
    </div>
</body>
</html>
