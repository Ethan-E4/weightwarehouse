<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f9;
            color: #333;
        }
        h1 {
            margin-bottom: 20px;
        }
        .cart-item {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-right: 20px;
        }
        .cart-item-details {
            flex: 1;
        }
        .cart-item form {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .buttons {
            margin-top: 30px;
        }
        .buttons a, .buttons button {
            padding: 10px 20px;
            margin-right: 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            background-color: #2c3e50;
            color: white;
            cursor: pointer;
        }
        .buttons a:hover, .buttons button:hover {
            background-color: #1a252f;
        }
    </style>
</head>
<body>

<h1>Your Shopping Cart</h1>

<?php
session_start();
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
    echo '<div class="buttons"><a href="index.php">Back to Shop</a></div>';
    exit;
}

$pdo = new PDO("mysql:host=127.0.0.1;dbname=WEIGHTS;charset=utf8", 'test_user', 'password', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$total = 0;

foreach ($_SESSION['cart'] as $id => $qty) {
    $stmt = $pdo->prepare("SELECT item_name, item_desc, image_path, price FROM items WHERE item_id = :id");
    $stmt->execute(['id' => $id]);
    $item = $stmt->fetch();

    if (!$item) continue;

    $subtotal = $item['price'] * $qty;
    $total += $subtotal;
    ?>

    <div class="cart-item">
        <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>">
        <div class="cart-item-details">
            <h2><?= htmlspecialchars($item['item_name']) ?></h2>
            <p><?= htmlspecialchars($item['item_desc']) ?></p>
            <p>Price: $<?= number_format($item['price'], 2) ?> | Subtotal: $<?= number_format($subtotal, 2) ?></p>

            <form method="post" action="update_cart.php">
                <input type="hidden" name="id" value="<?= $id ?>">
                <label for="qty">Qty:</label>
                <input type="number" name="qty" value="<?= $qty ?>" min="1" required>
                <button type="submit" name="update">Update</button>
                <button type="submit" name="remove" style="background-color: red;">Remove</button>
            </form>
        </div>
    </div>

<?php } ?>

<h2>Total: $<?= number_format($total, 2) ?></h2>

<div class="buttons">
    <a href="index.php">Back to Shop</a>
    <form method="post" action="checkout.php" style="display: inline;">
        <button type="submit">Checkout</button>
    </form>
</div>

</body>
</html>
