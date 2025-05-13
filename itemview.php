<html>
<head>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            padding: 30px;
            display: flex;
            gap: 30px;
        }

        .item-image {
            width: 400px;
            height: auto;
            border-radius: 10px;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .item-details h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }

        .item-details .price {
            font-size: 24px;
            color: #2c7a7b;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .item-details .description {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }
    </style>
</head>

<body>

<?php
$host = '127.0.0.1';
$db   = 'WEIGHTS';
$user = 'test_user';
$pass = 'password';

$dsn = "mysql:host=$host;dbname=$db;";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid item ID.";
    exit;
}

$sql = "SELECT item_id, item_name, item_desc, image_path, price FROM items WHERE item_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $_GET['id']]);
$item = $stmt->fetch();

if (!$item) {
    echo "Item not found.";
    exit;
}

echo "<div class='container'>";
echo "<img src='" . htmlspecialchars($item['image_path']) . "' class='item-image' alt='" . htmlspecialchars($item['item_name']) . "'>";
echo "<div class='item-details'>";
echo "<h1>" . htmlspecialchars($item['item_name']) . "</h1>";
echo "<div class='price'>\$" . number_format($item['price'], 2) . "</div>";
echo "<div class='description'>" . nl2br(htmlspecialchars($item['item_desc'])) . "</div>";
echo "</div>";
echo "</div>";
?>

</body>
</html>
