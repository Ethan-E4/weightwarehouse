<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .back-button {
            margin-bottom: 20px;
        }

        .back-button input[type="submit"] {
            padding: 10px 18px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .back-button input[type="submit"]:hover {
            background-color: #1a252f;
        }

        .item-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-wrap: wrap;
            overflow: hidden;
        }

        .item-image {
            flex: 1 1 400px;
            max-width: 500px;
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .item-details {
            flex: 1 1 400px;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .item-details h1 {
            font-size: 28px;
            margin-bottom: 15px;
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
        }
    </style>
</head>
<body>

<div class="wrapper">

    <form method="post" class="back-button">
        <input type="submit" value="Back to Listings">
    </form>

    <?php
    $host = '127.0.0.1';
    $db   = 'WEIGHTS';
    $user = 'test_user';
    $pass = 'password';
    $dsn  = "mysql:host=$host;dbname=$db;charset=utf8";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        echo "<p>Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
        exit;
    }

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo "<p>Invalid item ID.</p>";
        exit;
    }

    $stmt = $pdo->prepare("SELECT item_id, item_name, item_desc, image_path, price FROM items WHERE item_id = :id");
    $stmt->execute(['id' => $_GET['id']]);
    $item = $stmt->fetch();

    if (!$item) {
        echo "<p>Item not found.</p>";
        exit;
    }

    echo "<div class='item-card'>";
    echo "<img src='" . htmlspecialchars($item['image_path']) . "' class='item-image' alt='" . htmlspecialchars($item['item_name']) . "'>";
    echo "<div class='item-details'>";
    echo "<h1>" . htmlspecialchars($item['item_name']) . "</h1>";
    echo "<div class='price'>\$" . number_format($item['price'], 2) . "</div>";
    echo "<div class='description'>" . nl2br(htmlspecialchars($item['item_desc'])) . "</div>";
    echo "</div>";
    echo "</div>";
    ?>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: index.php");
    exit;
}
?>

</body>
</html>
