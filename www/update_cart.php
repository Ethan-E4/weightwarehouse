<?php
session_start();

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header("Location: view_cart.php");
    exit;
}

$id = (int)$_POST['id'];

if (isset($_POST['update']) && isset($_POST['qty']) && is_numeric($_POST['qty'])) {
    $qty = (int)$_POST['qty'];
    if ($qty > 0) {
        $_SESSION['cart'][$id] = $qty;
    }
} elseif (isset($_POST['remove'])) {
    unset($_SESSION['cart'][$id]);
}

header("Location: view_cart.php");
exit;
