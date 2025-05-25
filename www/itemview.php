<!DOCTYPE html>
<html>
<head>
    <title>Catalog</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .back-button {
            margin-bottom: 20px;
        }
        .back-button input[type="submit"] {
            padding: 10px 18px;
            background-color: #2c3e50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .back-button input[type="submit"]:hover {
            background-color: #1a252f;
        }

        .item-card {
            display: flex;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
            overflow: hidden;
            flex-wrap: wrap;
        }

        .image-container {
            flex: 1 1 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: #fff;
        }

        .image-container img {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
            background-color: #fff;
        }

        .item-details {
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .item-details h1 {
            font-size: 28px;
            margin-bottom: 15px;
            line-height: 1.2;
        }
        .item-details .price {
            font-size: 22px;
            font-weight: bold;
            color: #2c7a7b;
            margin-bottom: 20px;
        }
        .item-details .description {
            font-size: 15px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .item-details form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .item-details select, .item-details button {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 4px;
        }

        .item-details button {
            background-color: #2c3e50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .item-details button:hover {
            background-color: #1a252f;
        }

        @media (max-width: 700px) {
            .item-card {
                display: block;
            }
            .image-container {
                padding-top: 75%;
            }
            .item-details {
                padding: 20px;
            }
        }

        .message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 12px 20px;
            margin-bottom: 20px;
            border-left: 5px solid #3c763d;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <form method="post" class="back-button">
        <input type="submit" name="back" value="Back to Listings">
    </form>


<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['back'])) {
    header('Location: index.php');
    exit;
}

$host = '127.0.0.1'; $db = 'WEIGHTS';
$user = 'test_user'; $pass = 'password';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8";
$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) exit('Invalid item ID.');
$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT item_name,item_desc,image_path,price FROM items WHERE item_id = :id");
$stmt->execute(['id' => $id]);
$item = $stmt->fetch() ?: exit('Item not found.');

// Handle form submission for add-to-cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity']) && is_numeric($_POST['quantity'])) {
    $qty = (int)$_POST['quantity'];
    if ($qty > 0) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $qty;
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
        echo "<div class='message'>Added $qty item(s) to cart.</div>";
    }
}

echo "<div class='item-card'>";
    echo "<div class='image-container'>";
        echo "<img src='".htmlspecialchars($item['image_path'])."' alt='".htmlspecialchars($item['item_name'])."'>";
    echo "</div>";
    echo "<div class='item-details'>";
        echo "<h1>".htmlspecialchars($item['item_name'])."</h1>";
        echo "<div class='price'>$".number_format($item['price'], 2)."</div>";
        echo "<div class='description'>".nl2br(htmlspecialchars($item['item_desc']))."</div>";

        // Add-to-cart form
        echo "<form method='post'>";
            echo "<label for='quantity'>Qty:</label>";
            echo "<select name='quantity' id='quantity'>";
            for ($i = 1; $i <= 10; $i++) {
                echo "<option value='$i'>$i</option>";
            }
            echo "</select>";
            echo "<button type='submit'>Add to Cart</button>";
        echo "</form>";
    echo "</div>";
echo "</div>";
?>
</div>

</body>
</html>
