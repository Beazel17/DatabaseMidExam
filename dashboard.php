<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alcohol POS Dashboard</title>
    <style>
        /* Global styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: #f1f1f1;
        }

        /* Navbar styles */
        .navbar {
            background-color: #000;
            color: #f1f1f1;
            padding: 1em;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar .logo {
            font-weight: bold;
            color: #f05454;
        }

        .navbar a {
            color: #f05454;
            margin-left: 1em;
            text-decoration: none;
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: #ffffff;
        }

        /* Dashboard content styles */
        .dashboard {
            padding: 2em;
            max-width: 600px;
            margin: auto;
        }

        .dashboard h1 {
            color: #f05454;
        }

        .product-selection, .checkout {
            margin-top: 20px;
            background-color: #1f1f1f;
            padding: 15px;
            border-radius: 8px;
        }

        .product-selection label, .checkout label {
            color: #f1f1f1;
            font-weight: bold;
        }

        .product-selection select, .product-selection input, .checkout input {
            padding: 10px;
            margin-top: 10px;
            width: 100%;
            background-color: #333;
            color: #f1f1f1;
            border: 1px solid #444;
            border-radius: 5px;
        }

        /* Checkout button styles */
        .checkout button {
            background-color: #f05454;
            color: #f1f1f1;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 15px;
            transition: background-color 0.3s;
            cursor: pointer;
        }

        .checkout button:hover {
            background-color: #ff4e4e;
        }

        /* Message styles */
        .warning {
            font-weight: bold;
            margin-top: 10px;
        }

        .warning.success {
            color: #4caf50;
        }

        .warning.error {
            color: #f05454;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar">
    <div class="logo">Alcohol POS</div>
    <div>
        <a href="history.php">History Log</a>
        <a href="dashboard.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- Dashboard Section -->
<div class="dashboard">
    <h1>Point of Sale - Alcohol Products</h1>

    <!-- Product Selection -->
    <div class="product-selection">
        <label for="product">Select Product:</label>
        <select id="product">
            <option value="1" data-price="15">Whiskey - $15</option>
            <option value="2" data-price="10">Vodka - $10</option>
            <option value="3" data-price="12">Rum - $12</option>
            <option value="4" data-price="18">Tequila - $18</option>
            <option value="5" data-price="14">Gin - $14</option>
            <option value="6" data-price="16">Brandy - $16</option>
            <option value="7" data-price="8">Wine - $8</option>
            <option value="8" data-price="5">Beer - $5</option>
            <option value="9" data-price="7">Cider - $7</option>
            <option value="10" data-price="20">Champagne - $20</option>
        </select>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" min="1" placeholder="Enter quantity">
    </div>

    <!-- Checkout Section -->
    <div class="checkout">
        <label for="payment">Payment Amount:</label>
        <input type="number" id="payment" placeholder="Enter payment amount">

        <button onclick="processTransaction()">Checkout</button>

        <div id="message" class="warning"></div>
    </div>
</div>

<script>
    // JavaScript to handle POS logic
    function processTransaction() {
        const product = document.getElementById("product");
        const productId = product.value;
        const quantity = parseInt(document.getElementById("quantity").value);
        const payment = parseFloat(document.getElementById("payment").value);
        const message = document.getElementById("message");

        // Validate inputs
        if (isNaN(quantity) || isNaN(payment) || quantity <= 0 || payment <= 0) {
            message.style.color = 'red';
            message.innerText = 'Please enter valid quantity and payment amount.';
            return;
        }

        // Send transaction data to server
        fetch('process_transaction.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `product_id=${productId}&quantity=${quantity}&payment=${payment}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                message.style.color = 'green';
                message.innerText = `Transaction successful! Change: $${data.change.toFixed(2)}`;
            } else {
                message.style.color = 'red';
                message.innerText = data.message;
            }
        })
        .catch(error => {
            message.style.color = 'red';
            message.innerText = 'Error processing transaction. Please try again.';
        });
    }
</script>

</body>
</html>
