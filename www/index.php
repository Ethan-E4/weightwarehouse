<!DOCTYPE html>
<html lang="en">
<head>
    <title>Weight Warehouse - Gym Exercise Equipment</title>
    <style>
        /* Reset & global */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }
        a { text-decoration: none; color: inherit; }

        /* Header */
        header {
            background: #2c3e50;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        header h1 { font-size: 32px; }

        /* Filter bar */
        .filter-bar {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .filter-bar select,
        .filter-bar button {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #bbb;
        }
        .filter-bar button {
            background: #2c3e50;
            color: #fff;
            cursor: pointer;
        }

        /* Grid */
        .item-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto 40px;
            padding: 0 20px;
        }

        /* Card */
        .item-card {
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }
        .item-card:hover { transform: translateY(-5px); }

        /* Image wrapper for fixed aspect ratio */
        .image-wrapper {
            position: relative;
            width: 100%;
            padding-top: 75%; /* 4:3 aspect ratio */
            background-color: #fff;
            overflow: hidden;
        }
        .item-image {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain; /* preserve aspect ratio, show entire image */
            background-color: #fff;
        }

        .item-body {
            flex: 1;
            padding: 15px;
        }
        .item-body h2 {
            font-size: 18px;
            margin-bottom: 8px;
        }
        .item-body .price {
            color: #2c7a7b;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .item-body .desc {
            font-size: 14px;
            color: #555;
            margin-bottom: 12px;
            line-height: 1.4;
        }
        .item-body .types {
            font-size: 12px;
            color: #888;
        }

        /* Footer */
        footer {
            background: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<header>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Weight Warehouse</h1>
        <a href="view_cart.php" style="color: #fff; position: relative; margin-right: 10px;">
            <img src="images/icons8-cart-24.png" alt="Cart" style="width: 24px; height: 24px;">
        </a>

    </div>
</header>

<?php
// Session start for cart items
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // blank array
}

// DATABASE CONNECTION
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
$pdo = new PDO($dsn, $user, $pass, $options);

// BUILD TYPE LIST
$typeList = [];
$typeStmt = $pdo->query("SELECT `type` FROM items");
while ($row = $typeStmt->fetch()) {
    foreach (explode(',', $row['type']) as $t) {
        $t = trim($t);
        if ($t && !in_array($t, $typeList, true)) {
            $typeList[] = $t;
        }
    }
}
sort($typeList);

// GET CURRENT FILTER
$selectedType = $_GET['type'] ?? '';

// FETCH ITEMS WITH OPTIONAL FILTER
if ($selectedType && in_array($selectedType, $typeList, true)) {
    $sql = "SELECT * FROM items WHERE `type` LIKE :t";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['t' => "%{$selectedType}%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM items");
}
?>

<div class="filter-bar">
    <form method="get" style="display:flex; align-items:center; gap:10px;">
        <label for="type">Filter by type:</label>
        <select name="type" id="type">
            <option value="">All</option>
            <?php foreach ($typeList as $t): ?>
                <option value="<?= htmlspecialchars($t) ?>"
                    <?= $t === $selectedType ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t) ?>
                </option>
            <?php endforeach ?>
        </select>
        <button type="submit">Apply</button>
    </form>
</div>

<div class="item-container">
    <?php while ($item = $stmt->fetch()): ?>
        <a href="itemview.php?id=<?= $item['item_id'] ?>">
            <div class="item-card">
                <div class="image-wrapper">
                    <img src="<?= htmlspecialchars($item['image_path']) ?>"
                         alt="<?= htmlspecialchars($item['item_name']) ?>"
                         class="item-image">
                </div>
                <div class="item-body">
                    <h2><?= htmlspecialchars($item['item_name']) ?></h2>
                    <div class="price">$<?= number_format($item['price'],2) ?></div>
                    <div class="desc"><?= htmlspecialchars($item['item_desc']) ?></div>
                    <div class="types">
                        Type:
                        <?= htmlspecialchars($item['type']) ?>
                    </div>
                </div>
            </div>
        </a>
    <?php endwhile ?>
</div>

<footer>
    <div style="margin-bottom: 10px;">
        <a href="about.php" style="color: #fff; margin: 0 10px;">About Us</a> |
        <a href="contact.php" style="color: #fff; margin: 0 10px;">Contact Us</a> |
        <a href="pickup-policy.php" style="color: #fff; margin: 0 10px;">Pickup Policy</a>
    </div>
    &copy; <?= date('Y') ?> Weight Warehouse. All rights reserved.
</footer>

</body>
</html>
