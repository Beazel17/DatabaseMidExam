<?php
include 'config.php'; // Include your database connection file

// Fetch transactions for Left Join
function fetchLeftJoinTransactions() {
    global $pdo;
    $stmt = $pdo->query("SELECT t.id, u.username, p.name AS product_name, t.quantity, t.total_amount, t.payment, t.created_at 
                          FROM transactions t 
                          LEFT JOIN users u ON t.user_id = u.id 
                          LEFT JOIN products p ON t.product_id = p.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch transactions for Right Join
function fetchRightJoinTransactions() {
    global $pdo;
    $stmt = $pdo->query("SELECT t.id, u.username, p.name AS product_name, t.quantity, t.total_amount, t.payment, t.created_at 
                          FROM transactions t 
                          RIGHT JOIN users u ON t.user_id = u.id 
                          RIGHT JOIN products p ON t.product_id = p.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch transactions for Union Join
function fetchUnionTransactions() {
    global $pdo;
    $stmt = $pdo->query("SELECT t.id AS transaction_id, u.username, t.total_amount 
                          FROM transactions t 
                          JOIN users u ON t.user_id = u.id 
                          UNION 
                          SELECT NULL, u.username, NULL 
                          FROM users u");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$leftJoinTransactions = fetchLeftJoinTransactions();
$rightJoinTransactions = fetchRightJoinTransactions();
$unionTransactions = fetchUnionTransactions();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 2em;
        }
        .button-container {
            margin-bottom: 20px;
        }
        button {
            margin-right: 10px;
            padding: 10px 20px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Transaction History</h1>
    <div class="button-container">
        <button onclick="showTable('leftJoin')">Left Join</button>
        <button onclick="showTable('rightJoin')">Right Join</button>
        <button onclick="showTable('unionJoin')">Union Join</button>
    </div>

    <!-- Table for Left Join -->
    <div id="leftJoin" class="table-container">
        <h2>Left Join Transactions</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Total Amount</th>
                <th>Payment</th>
                <th>Date</th>
            </tr>
            <?php foreach ($leftJoinTransactions as $transaction): ?>
                <tr>
                    <td><?php echo $transaction['id']; ?></td>
                    <td><?php echo $transaction['username'] ?? 'N/A'; ?></td>
                    <td><?php echo $transaction['product_name'] ?? 'N/A'; ?></td>
                    <td><?php echo $transaction['quantity']; ?></td>
                    <td><?php echo $transaction['total_amount']; ?></td>
                    <td><?php echo $transaction['payment']; ?></td>
                    <td><?php echo $transaction['created_at']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Table for Right Join -->
    <div id="rightJoin" class="table-container hidden">
        <h2>Right Join Transactions</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Total Amount</th>
                <th>Payment</th>
                <th>Date</th>
            </tr>
            <?php foreach ($rightJoinTransactions as $transaction): ?>
                <tr>
                    <td><?php echo $transaction['id']; ?></td>
                    <td><?php echo $transaction['username'] ?? 'N/A'; ?></td>
                    <td><?php echo $transaction['product_name'] ?? 'N/A'; ?></td>
                    <td><?php echo $transaction['quantity']; ?></td>
                    <td><?php echo $transaction['total_amount']; ?></td>
                    <td><?php echo $transaction['payment']; ?></td>
                    <td><?php echo $transaction['created_at']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Table for Union Join -->
    <div id="unionJoin" class="table-container hidden">
        <h2>Union Join Transactions</h2>
        <table>
            <tr>
                <th>Transaction ID</th>
                <th>Username</th>
                <th>Total Amount</th>
            </tr>
            <?php foreach ($unionTransactions as $transaction): ?>
                <tr>
                    <td><?php echo $transaction['transaction_id'] ?? 'N/A'; ?></td>
                    <td><?php echo $transaction['username']; ?></td>
                    <td><?php echo $transaction['total_amount'] ?? 'N/A'; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<script>
    function showTable(tableName) {
        // Hide all table containers
        const tables = document.querySelectorAll('.table-container');
        tables.forEach(table => table.classList.add('hidden'));

        // Show the selected table
        document.getElementById(tableName).classList.remove('hidden');
    }

    // Show the left join table by default
    showTable('leftJoin');
</script>

</body>
</html>
