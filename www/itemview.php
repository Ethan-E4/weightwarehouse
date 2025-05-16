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

        /* Grid layout: two columns, image is fixed ratio container */
        .item-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* Left column: keep 4:3 */
        .image-container {
            position: relative;
            width: 100%;
            /* enforce 4:3 box */
            padding-top: 75%; 
            overflow: hidden;
        }
        .image-container img {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            object-fit: cover;
        }

        /* Right column: text details */
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
        }

        /* Responsive: stack on narrow screens */
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
    </style>
</head>
<body>

<div class="wrapper">

    <form method="post" class="back-button">
        <input type="submit" value="Back to Listings">
    </form>

    <?php
    // — your existing connection code —
    $host = '127.0.0.1'; $db = 'WEIGHTS';
    $user = 'test_user'; $pass = 'password';
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    ]);

    if (!isset($_GET['id'])||!is_numeric($_GET['id'])) exit('Invalid item ID.');
    $stmt = $pdo->prepare("SELECT item_name,item_desc,image_path,price FROM items WHERE item_id = :id");
    $stmt->execute(['id'=>$_GET['id']]);
    $item = $stmt->fetch() ?: exit('Item not found.');

    // — new markup structure —
    echo "<div class='item-card'>";
      // image column
      echo "<div class='image-container'>";
        echo "<img src='".htmlspecialchars($item['image_path'])."' alt='".htmlspecialchars($item['item_name'])."'>";
      echo "</div>";
      // details column
      echo "<div class='item-details'>";
        echo "<h1>".htmlspecialchars($item['item_name'])."</h1>";
        echo "<div class='price'>$".number_format($item['price'],2)."</div>";
        echo "<div class='description'>".nl2br(htmlspecialchars($item['item_desc']))."</div>";
      echo "</div>";
    echo "</div>";
    ?>
</div>

<?php
if ($_SERVER['REQUEST_METHOD']==='POST') {
    header('Location: index.php');
    exit;
}
?>
</body>
</html>
