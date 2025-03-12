<?php
session_start();

include 'connection/connection.php';


if (!isset($_SESSION['total']) || empty($_SESSION['total'])) {
    die("Error: Order total is missing.");
}


$order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : null;

if ($order_id) {
   
    $sql = "SELECT user_province FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id); 
    $stmt->execute();

    if ($stmt->error) {
        die("Query Error: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $province = $row['user_province'];
    } else {
        die("Error: Province not found for this order.");
    }
} else {
    die("Error: Order ID not set in the session.");
}

// Set shipping fee based on the selected province
$shipping_fee = 0;

if ($province == 'Western') {
    $shipping_fee = 250;  
} elseif ($province == 'Other') {
    $shipping_fee = 350; 
}

// Add shipping fee to total amount
$total_with_shipping = $_SESSION['total'] + $shipping_fee;


$sql_update = "UPDATE orders SET shipping_fee = ?, total_cost = ? WHERE order_id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("dii", $shipping_fee, $total_with_shipping, $order_id);

if ($stmt_update->execute()) {
 
    $_SESSION['success_message'] = "Order updated with shipping fee and total cost.";
} else {
    die("Error: " . $stmt_update->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To pay - Gifty House</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Global Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Back Button */
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 1.5rem;
            color: #333333;
            text-decoration: none;
            background-color: transparent;
            padding: 10px 15px;
            border-radius: 50%;
            border: 2px solid #D4AF37;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .back-button:hover {
            background-color: #D4AF37;
            color: #ffffff;
            transform: scale(1.1);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .back-button i {
            font-size: 1.5rem;
        }

        /* Section Styling */
        section {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            padding: 40px 30px;
            max-width: 500px;
            margin: 100px auto;
        }

        /* Title Styling */
        .form-weight-bold {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        /* Order Status and Total Payment */
        .order-status {
            font-size: 1.1rem;
            color: #7f8c8d;
            margin-bottom: 25px;
            text-align: center;
        }

        .total-payment {
            font-size: 1.3rem;
            font-weight: 600;
            color: #F26D6D;
            margin-bottom: 35px;
            text-align: center;
        }

        /* Button Styling */
        .btn {
            background-color: #D4AF37;
            color: #fff;
            padding: 12px 35px;
            font-size: 1.1rem;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            cursor: pointer;
            width: 100%;
            display: block;
            margin: 0 auto;
        }

        .btn:hover {
            background-color: #F26D6D;
            color: #fff;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            section {
                padding: 35px 20px;
            }

            .form-weight-bold {
                font-size: 1.8rem;
            }

            .btn {
                font-size: 1rem;
                padding: 12px 25px;
            }
        }

        /* Separator line style */
        hr {
            border: 0;
            height: 2px;
            background-color: #D4AF37;
            width: 50px;
            margin: 20px auto;
        }
    </style>
</head>
<body>

<!-- Back Button -->
<a href="shipping.php" class="back-button" title="Go Back">
    <i class="bi bi-arrow-left"></i>
</a>

<section>
    <div class="container text-center">
        <h2 class="form-weight-bold">Checkout - Payment</h2>
        <hr>
    </div>

    <div class="container">
        <p class="order-status"><?php echo $_GET['order_status']; ?></p>

        <p class="order-status">Shipping Fee: Rs. <?php echo $shipping_fee; ?> (Based on province: <?php echo $province; ?>)</p>
        
        <p class="total-payment">Total Payment (including shipping): Rs. <?php echo $total_with_shipping; ?></p>
        <a href="payment.php" class="btn">Proceed to Payment</a>
    </div>
</section>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
