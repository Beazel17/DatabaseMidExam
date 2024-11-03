<?php
session_start();
include 'config.php'; // Assume a separate file to handle database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user details
$userId = $_SESSION['user_id'];
$userQuery = $db->prepare("SELECT * FROM users WHERE id = ?");
$userQuery->execute([$userId]);
$user = $userQuery->fetch();

// Fetch products
$productsQuery = $db->query("SELECT * FROM products");
$products = $productsQuery->fetchAll();

// Handle purchase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'], $_POST['payment'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $payment = $_POST['payment'];

    // Get product details
    $productQuery = $db->prepare("SELECT * FROM products WHERE id = ?");
    $productQuery->execute([$productId]);
    $product = $productQuery->fetch();

    if ($product) {
        $totalCost = $product['price'] * $quantity;

        // Check stock and payment
        if ($quantity > $product['stock']) {
            echo "<p style='color:red;'>Not enough stock available.</p>";
        } elseif ($payment < $totalCost) {
            echo "<p style='color:red;'>Insufficient payment. Total cost is $totalCost.</p>";
        } else {
            // Deduct stock and record transaction
            $newStock = $product['stock'] - $quantity;
            $updateStockQuery = $db->prepare("UPDATE products SET stock = ? WHERE id = ?");
            $updateStockQuery->execute([$newStock, $productId]);

            $insertTransaction = $db->prepare("
                INSERT INTO transactions (user_id, product_id, quantity, total_amount, payment)
                VALUES (?, ?, ?, ?, ?)
            ");
            $insertTransaction->execute([$userId, $productId, $quantity, $totalCost, $payment]);

            echo "<p style='color:green;'>Purchase successful!</p>";
        }
    }
}

// Fetch transaction history
$historyQuery = $db->query("
    SELECT u.username, p.name as product_name, t.quantity, t.total_amount, t.payment, t.created_at 
    FROM transactions t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN products p ON t.product_id = p.id
    ORDER BY t.created_at DESC
");
$history = $historyQuery->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; text-align: center; }
        .button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        .button:hover { background: #0056b3; }
    </style>
</head>
<body>

<h1>Welcome, <?= htmlspecialchars($user['username']); ?>!</h1>

<h2>Buy Alcohol Products</h2>
<form method="POST" action="">
    <label for="product_id">Product:</label>
    <select name="product_id" id="product_id" required>
        <?php foreach ($products as $product): ?>
            <option value="<?= $product['id']; ?>"><?= $product['name']; ?> - $<?= $product['price']; ?> (Stock: <?= $product['stock']; ?>)</option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" min="1" required>
    <br><br>

    <label for="payment">Payment Amount:</label>
    <input type="number" name="payment" id="payment" min="1" required>
    <br><br>

    <button type="submit" class="button">Purchase</button>
</form>

<h2>Purchase History</h2>
<table>
    <tr>
        <th>Username</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Total Amount</th>
        <th>Payment</th>
        <th>Date</th>
    </tr>
    <?php foreach ($history as $record): ?>
        <tr>
            <td><?= htmlspecialchars($record['username']); ?></td>
            <td><?= htmlspecialchars($record['product_name']); ?></td>
            <td><?= htmlspecialchars($record['quantity']); ?></td>
            <td>$<?= htmlspecialchars($record['total_amount']); ?></td>
            <td>$<?= htmlspecialchars($record['payment']); ?></td>
            <td><?= htmlspecialchars($record['created_at']); ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Database Joins</h2>
<button onclick="window.location.href='join_view.php?join=left'" class="button">View Left Join</button>
<button onclick="window.location.href='join_view.php?join=right'" class="button">View Right Join</button>
<button onclick="window.location.href='join_view.php?join=union'" class="button">View Union Join</button>

</body>
</html>
