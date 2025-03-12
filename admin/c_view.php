<?php
require '../connection/connection.php';


if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "No user found with ID: $user_id";
    }

    $order_sql = "SELECT * FROM order_item WHERE user_id = ?";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("i", $user_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
} else {
    echo "No user ID provided.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Details</title>
    <link rel="icon" href="../logo.png" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="css/styles.css">

    <style>
        html,
        body {
            height: 100%;
            overflow-y: auto !important;
        }

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
            margin-bottom: 50px;
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

        .container {
            max-width: 3200px;
            margin: 0 auto;
            /* Center container */
            padding: 30px;

        }

        .card {
            min-height: 185vh;
            width: 100%;
            padding-bottom: 20px;
        }


        h2.customer-details {
            font-size: 2.5rem;
            font-weight: 600;
            color: #333;
            text-align: center;
            margin-bottom: 40px;
        }

        .order-details {
            font-size: 2.5rem;
            font-weight: 600;
            color: #333;
            text-align: center;
            margin-bottom: 40px;
        }

        h3 {
            font-size: 1.75rem;
            font-weight: 500;
            color: #333;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .table th {
            background-color: #f0f0f0;
            color: #333;
            font-weight: 600;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .order-table td,
        .order-table th {
            font-size: 1.1rem;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            /* Ensures it's at the right corner */
            font-size: 1.5rem;
            color: #333;
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


        .order-not {
            color: #333 !important;
            font-size: 16px !important;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .table th,
            .table td {
                font-size: 0.9rem;
            }

            h2.customer-details {
                font-size: 2rem;
            }

            .back-button {
                font-size: 1.2rem;
                padding: 10px 15px;
            }
        }
    </style>
</head>

<body>



    <div class="container mt-5">
        <a href="customer_list.php" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h2 class="customer-details">Customer Details</h2>
        <?php if (isset($user)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact Number</th>
                            <th>Address</th>
                            <th>Country</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['full_name']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['contact_number']; ?></td>
                            <td><?php echo $user['address']; ?></td>
                            <td><?php echo $user['country']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No details available.</p>
        <?php endif; ?>

        <!-- Order Details -->
        <h3 class="order-details mt-5">Order Details</h3>
        <?php if ($order_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered order-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product Name</th>
                            <th>Order Date</th>
                            <th>Amount</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $order_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $order['order_id']; ?></td>
                                <td><?php echo $order['product_name']; ?></td>
                                <td><?php echo $order['order_date']; ?></td>
                                <td>Rs. <?php echo $order['product_price']; ?></td>
                                <td><?php echo $order['product_quantity']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="order-not">No orders found for this customer.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        © 2025 Gifty House. All Rights Reserved.
    </footer>

</body>

</html>