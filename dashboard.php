<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alcohol POS Dashboard</title>
    <style>
        /* Basic CSS for layout */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            color: white;
            padding: 1em;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .navbar .logo {
            font-weight: bold;
        }
        .navbar a {
            color: white;
            margin-left: 1em;
            text-decoration: none;
        }
        .dashboard {
            padding: 2em;
        }
        .product-selection, .checkout {
            margin-top: 20px;
        }
        .product-selection select, .product-selection input, .checkout input {
            padding: 8px;
            margin-top: 5px;
            width: 100%;
        }
        .warning {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar">
    <div class="logo">Alcohol POS</div>
    <div>
        <a href="#history">History Log</a>
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
