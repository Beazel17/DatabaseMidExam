<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $user_id = 1; // Example user ID; in real case, get from session or login system
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $payment = $_POST['payment'];

    try {
        // Get the product price from the database
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = :product_id");
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $price = $product['price'];
            $total_amount = $price * $quantity;

            if ($payment >= $total_amount) {
                // Insert the transaction into the database
                $stmt = $pdo->prepare("INSERT INTO transactions (user_id, product_id, quantity, total_amount, payment) VALUES (:user_id, :product_id, :quantity, :total_amount, :payment)");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                $stmt->bindParam(':total_amount', $total_amount);
                $stmt->bindParam(':payment', $payment);
                $stmt->execute();

                echo json_encode([
                    "status" => "success",
                    "change" => $payment - $total_amount
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Insufficient payment. Please enter a higher amount."
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Product not found."
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
}
?>
