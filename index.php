<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        p {
            font-size: 16px;
            color: #666;
            line-height: 1.5;
        }

        a {
        text-decoration: none;     
        color: inherit;            
        }

        .item-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
            padding: 20px;
        }

        .item-card {
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 250px;
            text-align: center;
            padding: 20px;
            transition: transform 0.3s ease;
        }

        .item-card:hover {
            transform: translateY(-5px);
        }

        .item-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .item-card h1 {
            font-size: 20px;
            margin-top: 15px;
        }

        .item-card p {
            font-size: 14px;
            color: #555;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<?php
    $host = '127.0.0.1';       // or 'localhost'
    $db   = 'WEIGHTS';         // your database name
    $user = 'test_user';       // your DB username
    $pass = 'password';        // your DB password

    $dsn = "mysql:host=$host;dbname=$db;";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // better error reporting
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // return associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                  // use native prepares
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        // echo "Database connection successful."; // Removed for cleaner UI
    } catch (PDOException $e) {
        echo "Database connection failed: " . $e->getMessage();
    }

    // Fetching items from the database
    $sql = "SELECT item_id, item_name, item_desc, image_path, price FROM items";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
?>

<div class="item-container">
    <?php
        while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<a href='itemview.php?id=" . $item['item_id'] . "'>";
            echo "<div class='item-card'>";
            echo "<img src='" . $item["image_path"] . "' alt='" . $item["item_name"] . "' class='item-image'>";
            echo "<h1>" . $item["item_name"] . "</h1>";
            echo "<p> $" . $item['price'] . "</p>";
            echo "</div>";
            echo "</a>";
        }
    ?>
</div>

</body>
</html>
