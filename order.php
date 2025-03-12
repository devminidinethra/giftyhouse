<?php
session_start();
include 'connection/connection.php';

// Handle logout request
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_unset();
    session_destroy();
    header('Location: forms/login.php');
    exit;
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY FIELD(order_status, 'Processing') DESC, order_date DESC");
    $stmt->bind_param('i', $user_id);

    $stmt->execute();

    $orders = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJv0l4p8Sg0t9vPpbiJtDWE65FjxE2nwb0d0HZJXkI15UgaJgEZHpzzlj7Q3" crossorigin="anonymous">


    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .order-header {
            position: relative;
            background-image: url('img/main.jpg');
            background-size: cover;
            background-position: center;
            height: 60vh;
            color: white;
            text-align: left;
            padding: 0;
        }

        .order-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .order-header-text {
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            z-index: 2;
            color: #D4AF37;
            font-size: 5rem;
            font-weight: 700;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
            animation: slideInUp 1.5s ease-out;
        }

        .order-header-text p {
            font-size: 2rem;
            color: #F7E7CE;
            margin-top: 1rem;
            font-weight: 300;
        }

        .order-header-text h1 {
            font-size: 4rem;
            font-weight: 700;
            margin: 0;
            color: #FFD700;
        }


        /* Header Styling */
        .order-header {
            background-color: #2c3e50;
            color: white;
            padding: 40px 0;
            text-align: center;
            margin-bottom: 30px;
        }

        .order-header h1 {
            font-size: 2rem;
            font-weight: bold;
        }

        /* Table Container - Smaller Width */
        .order-table-container {
            max-width: 90%;
            margin: auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-bottom: 30px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;

        }

        thead {
            background-color: #D4AF37;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        th,
        td {
            text-align: center;
            padding: 12px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        tr:hover {
            background-color: #f1f1f1;
            transition: 0.3s;
        }

        /* Status Badge */
        .status {
            padding: 6px 10px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            min-width: 80px;
        }

        .status.on_hold {
            background-color: #FFC107;
            color: white;
        }

        .status.completed {
            background-color: #28A745;
            color: white;
        }

        .status.cancelled {
            background-color: #DC3545;
            color: white;
        }

        /* Order Details Button */
        .order-detail-btn {
            background-color: #D4AF37;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .order-detail-btn:hover {
            background-color: #F26D6D;
            color: white;
            transform: scale(1.05);
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            .order-table-container {
                max-width: 95%;
                padding: 15px;
            }

            th,
            td {
                padding: 10px;
                font-size: 12px;
            }

            .order-detail-btn {
                padding: 6px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

    <!-- PHP Include Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="order-header">
        <div class="order-header-text">
            <h1>My Orders</h1>
            <p>Track and manage your purchases easily.</p>
        </div>
    </div>

   <div class="order-table-container">
    <table class="table table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Order Cost (Rs.)</th>
                <th>Shipping Fee (Rs.)</th>
                <th>Total Cost (Rs.)</th>
                <th>Order Status</th>
                <th>Order Date</th>
                <th>Order Details</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($orders->num_rows > 0) {
                while ($row = $orders->fetch_assoc()) { ?>
                    <tr>
                        <td><strong><?php echo $row['order_id']; ?></strong></td>
                        <td>Rs. <?php echo number_format($row['order_cost'], 2); ?></td>
                        <td>Rs. <?php echo number_format($row['shipping_fee'], 2); ?></td>
                        <td>Rs. <?php echo number_format($row['total_cost'], 2); ?></td>
                        <td>
                            <span class="status 
                                <?php echo strtolower(str_replace(' ', '_', $row['order_status'])); ?>">
                                <?php echo ucfirst($row['order_status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d M Y', strtotime($row['order_date'])); ?></td>
                        <td>
                            <a href="order_details.php?order_id=<?php echo $row['order_id']; ?>" class="order-detail-btn">
                                View Details
                            </a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="7">No orders found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>


    <!-- Footer -->
    <?php include 'footer.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>