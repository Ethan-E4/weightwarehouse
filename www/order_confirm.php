<?php
session_start();

if (
    !isset($_SESSION['pickup'], $_SESSION['cart']) ||
    !is_array($_SESSION['cart']) || 
    empty($_SESSION['cart'])
) {
    header("Location: pickupform.php");
    exit();
}

$pickup = $_SESSION['pickup'];
$cart = $_SESSION['cart'];

$uniqueIds = array_keys($cart); // item_id => quantity

if (empty($uniqueIds)) {
    die("Your cart is empty or contains invalid items.");
}

try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1;dbname=WEIGHTS;charset=utf8",
        'test_user',
        'password',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $placeholders = implode(',', array_fill(0, count($uniqueIds), '?'));
    $stmt = $pdo->prepare("SELECT item_id, item_name, price FROM items WHERE item_id IN ($placeholders)");
    $stmt->execute($uniqueIds);

    $fetchedItems = $stmt->fetchAll();

    $items = [];
    foreach ($fetchedItems as $item) {
        $id = $item['item_id'];
        if (isset($cart[$id])) {
            $quantity = $cart[$id];
            $item['quantity'] = $quantity;
            $item['subtotal'] = $item['price'] * $quantity;
            $items[] = $item;
        }
    }

    if (empty($items)) {
        die("No valid items found for the provided IDs.");
    }

    $totalPrice = array_sum(array_column($items, 'subtotal'));

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            color: #333;
            margin: 0; padding: 20px;
        }
        .container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
        .message {
            background-color: #d4edda;
            border-left: 4px solid #3c763d;
            padding: 15px;
            border-radius: 5px;
            color: #155724;
        }
        .back-store-button {
            display: inline-block;
            margin: 20px;
            padding: 10px 15px;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .back-store-button:hover {
            background-color: #1a252f;
        }

    </style>
</head>
<body>

<a href="index.php" class="back-store-button">← Back to Store</a>

<div class="container">
    <h1>Order Confirmation</h1>

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Unit Price ($)</th>
                <th>Quantity</th>
                <th>Subtotal ($)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td><?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['subtotal'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="total">Total</td>
                <td class="total">$<?= number_format($totalPrice, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="message">
        Thank you, <?= htmlspecialchars($pickup['first_name']) ?>!<br>
        Your pickup is scheduled for <strong><?= htmlspecialchars($pickup['pickup_date']) ?></strong>.<br>
        A receipt will be sent to <strong><?= htmlspecialchars($pickup['email']) ?></strong>.
    </div>
</div>

<?php
// !! DONT FORGET TO REMOVE BEFORE PRODUCTION !! 
session_start(); // Start the session to access $_SESSION data

function dumpSessionData($data, $level = 0) {
    foreach ($data as $key => $value) {
        echo str_repeat("&nbsp;", $level * 4); // Indent for readability
        echo "<strong>{$key}:</strong> ";
        
        if (is_array($value)) {
            echo "<br>";
            dumpSessionData($value, $level + 1); // Recursively dump sub-arrays
        } else {
            echo htmlspecialchars($value) . "<br>";
        }
    }
}

echo "<h2>Session Data Dump</h2>";
if (!empty($_SESSION)) {
    dumpSessionData($_SESSION);
} else {
    echo "<p>No session data found.</p>";
}
?>

</body>

<?php

$_SESSION['cart'] = []; 
?>
</html>
