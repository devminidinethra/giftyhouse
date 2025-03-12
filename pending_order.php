<?php
session_start();
include('connection/connection.php');

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

//  get orders with "Not Paid" payment status 
$sql_payment = "SELECT o.order_id, o.order_status, o.order_date, oi.payment_status, oi.product_image, oi.product_name, oi.product_quantity, o.total_cost
                FROM orders o
                JOIN order_item oi ON o.order_id = oi.order_id
                WHERE oi.payment_status = 'Not Paid' AND o.user_id = '$user_id'";

// get orders with "Processing" status 
$sql_processing = "SELECT o.order_id, o.order_status, o.order_date, oi.payment_status, oi.product_image, oi.product_name, oi.product_quantity, o.total_cost
                   FROM orders o
                   JOIN order_item oi ON o.order_id = oi.order_id
                   WHERE o.order_status = 'Pending' AND o.user_id = '$user_id'";

// get orders with "Shipping" status 
$sql_shipping = "SELECT o.order_id, o.order_status, o.order_date, oi.payment_status, oi.product_image, oi.product_name, oi.product_quantity, o.total_cost
                 FROM orders o
                 JOIN order_item oi ON o.order_id = oi.order_id
                 WHERE o.order_status = 'Shipping' AND o.user_id = '$user_id'";

// Execute queries only if user is logged in
$result_payment = $user_id ? mysqli_query($conn, $sql_payment) : false;
$result_processing = $user_id ? mysqli_query($conn, $sql_processing) : false;
$result_shipping = $user_id ? mysqli_query($conn, $sql_shipping) : false;

// Handle the status update when "Delivered" button is clicked
if (isset($_POST['deliver_order_id']) && $user_id) {
    $order_id_to_update = $_POST['deliver_order_id'];
    $sql_update_status = "UPDATE orders SET order_status = 'Delivered' WHERE order_id = '$order_id_to_update' AND user_id = '$user_id'";
    mysqli_query($conn, $sql_update_status);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Status</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
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

        .order-card {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background-color: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .order-card-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            font-size: 1.25rem;
            font-weight: bold;
        }

        .order-card-body {
            padding: 20px;
        }

        .order-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }

        .order-details {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .order-details div {
            flex: 1;
            margin-left: 20px;
        }

        .order-status {
            padding: 6px 12px;
            border-radius: 25px;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-processing {
            background-color: #ffc107;
        }

        .status-payment-not-paid {
            background-color: #f26d6d;
        }

        .status-shipping {
            background-color: #17a2b8;
        }

        .order-total {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }

        .order-address {
            font-size: 0.875rem;
            color: #666;
        }

        @media (max-width: 768px) {
            .order-card-header {
                font-size: 1.1rem;
            }

            .order-image {
                width: 100px;
                height: 100px;
            }

            .order-total {
                font-size: 1rem;
            }

            .order-status {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .order-details {
                flex-direction: column;
                text-align: center;
            }

            .order-image {
                margin-bottom: 15px;
            }

            .order-card-body {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <a href="profile.php" class="back-button" title="Go Back">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="container mt-5">
        <h1 class="mb-4 text-center text-primary">Your Pending Orders</h1>

        <!-- Orders with 'Not Paid' Status -->
        <h3 class="text-center text-danger">Orders with 'Not Paid' Payment Status</h3>
        <div class="row">
            <?php
            if (mysqli_num_rows($result_payment) > 0) {
                while ($row = mysqli_fetch_assoc($result_payment)) {
                    $order_id = $row['order_id'];
                    $order_date = $row['order_date'];
                    $product_image = $row['product_image'];
                    $product_name = $row['product_name'];
                    $quantity = $row['product_quantity'];
                    $order_cost = $row['total_cost'];
            ?>
                    <div class="col-md-6 col-sm-12">
                        <div class="order-card">
                            <div class="order-card-header">
                                Order ID: #<?php echo $order_id; ?>
                            </div>
                            <div class="order-card-body">
                                <div class="order-details">
                                    <img src="admin/<?php echo $product_image; ?>" alt="Product Image" class="order-image">
                                    <div>
                                        <p><strong>Order Date:</strong> <?php echo date("F j, Y, g:i A", strtotime($order_date)); ?></p>
                                        <p><strong>Product Name:</strong> <?php echo $product_name; ?></p>
                                        <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
                                        <p><strong>Order Cost:</strong> Rs.<?php echo number_format($order_cost, 2); ?></p>
                                    </div>
                                    <span class="order-status status-payment-not-paid">Not Paid</span>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p class='text-center'>No orders with 'Not Paid' status found.</p>";
            }
            ?>
        </div>
        <!-- Orders with 'Processing' Status -->
        <h3 class="text-center text-warning">Orders with 'Pending' Status</h3>
        <div class="row">
            <?php
            if (mysqli_num_rows($result_processing) > 0) {
                while ($row = mysqli_fetch_assoc($result_processing)) {
                    $order_id = $row['order_id'];
                    $order_date = $row['order_date'];
                    $product_image = $row['product_image'];
                    $product_name = $row['product_name'];
                    $quantity = $row['product_quantity'];
                    $order_cost = $row['total_cost'];
            ?>
                    <div class="col-md-6 col-sm-12">
                        <div class="order-card">
                            <div class="order-card-header">
                                Order ID: #<?php echo $order_id; ?>
                            </div>
                            <div class="order-card-body">
                                <div class="order-details">
                                    <img src="admin/<?php echo $product_image; ?>" alt="Product Image" class="order-image">
                                    <div>
                                        <p><strong>Order Date:</strong> <?php echo date("F j, Y, g:i A", strtotime($order_date)); ?></p>
                                        <p><strong>Product Name:</strong> <?php echo $product_name; ?></p>
                                        <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
                                        <p><strong>Order Cost:</strong> Rs. <?php echo number_format($order_cost, 2); ?></p>
                                    </div>
                                    <span class="order-status status-processing">Pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p class='text-center'>No orders with 'Processing' status found.</p>";
            }
            ?>
        </div>

        <!-- Orders with 'Shipping' Status -->
        <h3 class="text-center text-info">Orders with 'Shipping' Status</h3>
        <div class="row">
            <?php
            if (mysqli_num_rows($result_shipping) > 0) {
                while ($row = mysqli_fetch_assoc($result_shipping)) {
                    $order_id = $row['order_id'];
                    $order_date = $row['order_date'];
                    $product_image = $row['product_image'];
                    $product_name = $row['product_name'];
                    $quantity = $row['product_quantity'];
                    $order_cost = $row['total_cost'];
            ?>
                    <div class="col-md-6 col-sm-12">
                        <div class="order-card">
                            <div class="order-card-header">
                                Order ID: #<?php echo $order_id; ?>
                            </div>
                            <div class="order-card-body">
                                <div class="order-details">
                                    <img src="admin/<?php echo $product_image; ?>" alt="Product Image" class="order-image">
                                    <div>
                                        <p><strong>Order Date:</strong> <?php echo date("F j, Y, g:i A", strtotime($order_date)); ?></p>
                                        <p><strong>Product Name:</strong> <?php echo $product_name; ?></p>
                                        <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
                                        <p><strong>Order Cost:</strong> Rs. <?php echo number_format($order_cost, 2); ?></p>
                                    </div>
                                    <span class="order-status status-shipping">Shipping</span>
                                </div>

                                <!-- Button to mark the order as Delivered -->
                                <form method="POST" action="">
                                    <button type="submit" name="deliver_order_id" value="<?php echo $order_id; ?>" class="btn btn-success mt-2">
                                        Mark as Delivered
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p class='text-center'>No orders with 'Shipping' status found.</p>";
            }
            ?>
        </div>

    </div>

    <?php include('footer.html'); ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
