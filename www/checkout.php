<?php
session_start();

$errors = [];
$success = false;

// Handle form submission only when any field is actually submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    // Collect and sanitize
    $firstName  = trim($_POST['first_name'] ?? '');
    $lastName   = trim($_POST['last_name']  ?? '');
    $email      = trim($_POST['email']      ?? '');
    $phone      = trim($_POST['phone']      ?? '');
    $pickupDate = trim($_POST['pickup_date'] ?? '');

    // Validate
    if ($firstName === '') {
        $errors[] = "First name is required.";
    }
    if ($lastName === '') {
        $errors[] = "Last name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (!preg_match('/^\d{7,15}$/', $phone)) {
        $errors[] = "Please enter a valid phone number (7–15 digits).";
    }
    $dt = DateTime::createFromFormat('Y-m-d', $pickupDate);
    if (!$dt || $dt->format('Y-m-d') !== $pickupDate) {
        $errors[] = "Please select a valid pickup date.";
    }

    // If no errors, store in session and redirect
    if (empty($errors)) {
        $_SESSION['pickup'] = [
            'first_name'  => $firstName,
            'last_name'   => $lastName,
            'email'       => $email,
            'phone'       => $phone,
            'pickup_date' => $pickupDate,
        ];
        header("Location: order_confirm.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Pickup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            color: #333;
            margin: 0; padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        form {
            display: grid;
            gap: 15px;
        }
        label {
            font-weight: bold;
        }
        input {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #bbb;
            border-radius: 4px;
            width: 100%;
        }
        input[type="date"] {
            max-width: 50%;
        }
        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .buttons button {
            padding: 10px 20px;
            background: #2c3e50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .buttons button:hover {
            background: #1a252f;
        }

        .back-button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .back-button:hover {
            background-color: #1a252f;
        }

        .errors {
            background: #f8d7da;
            border-left: 4px solid #a94442;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #721c24;
        }
    </style>
</head>
<body>

<a href="view_cart.php" class="back-button">← Back to Cart</a>

<div class="container">
    <h1>Schedule Pickup</h1>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div>
            <label for="first_name">First Name</label>
            <input id="first_name" name="first_name" type="text"
                   value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
        </div>
        <div>
            <label for="last_name">Last Name</label>
            <input id="last_name" name="last_name" type="text"
                   value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
        </div>
        <div>
            <label for="email">Email</label>
            <input id="email" name="email" type="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        <div>
            <label for="phone">Phone</label>
            <input id="phone" name="phone" type="tel" pattern="\d{7,15}"
                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
        </div>
        <div>
            <label for="pickup_date">Pickup Date</label>
            <input id="pickup_date" name="pickup_date" type="date"
                   min="<?= date('Y-m-d') ?>"
                   value="<?= htmlspecialchars($_POST['pickup_date'] ?? '') ?>" required>
        </div>
        <div class="buttons">
            <button type="submit">Submit</button>
        </div>

  /form>
</div>


</body>
</html>
